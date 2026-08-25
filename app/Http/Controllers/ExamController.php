<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExamsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $vesselName = $request->get('vessel_name');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $reviewStatus = $request->get('review_status');
        $perPage = 20;
        $currentPage = (int) $request->get('page', 1);

        // All active filters are sent to the API so it can filter and paginate
        // server-side across the full result set, not just the current page.
        $result = $this->api->getExams(array_filter([
            'search' => $search,
            'vessel_name' => $vesselName,
            'review_status' => $reviewStatus,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'page' => $currentPage,
            'per_page' => $perPage,
        ], fn ($v) => $v !== null && $v !== ''));

        $exams = $result['data']['data'] ?? $result['data'] ?? [];
        if (!is_array($exams)) $exams = [];
        $meta = $result['data']['meta'] ?? [];

        $paginatedExams = new LengthAwarePaginator(
            collect($exams)->values(),
            $meta['total'] ?? count($exams),
            $meta['per_page'] ?? $perPage,
            $meta['current_page'] ?? $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // The API delivers ONE flat row per person-in-charge (PIC); every PIC of a single
        // questionnaire upload shares the same vessel_name and upload_batch_id. Rather than
        // repeat the vessel on every row, we fold the current page into a
        // vessel -> upload-batch -> PIC tree so the vessel name is shown once.
        //
        // NOTE: the API paginates by ROW, so a very large batch could straddle a page
        // boundary and appear (partially) on two pages. Batches are normally a handful of
        // PICs, so we group only the rows on the current page and keep each page
        // self-consistent. Batch approve/reject still acts on the ENTIRE batch API-side.
        $vesselGroups = $this->groupExamsByVesselAndBatch($exams);

        // Vessel dropdown is sourced from the API's own distinct-vessel endpoint (scoped to
        // the caller), not from whatever exams happen to be on the current page.
        $vesselsResult = $this->api->getVessels();
        $vessels = $vesselsResult['data']['data'] ?? $vesselsResult['data'] ?? [];
        if (!is_array($vessels)) $vessels = [];
        $vessels = collect($vessels)->filter()->sort()->values()->toArray();

        return view('exams.index', [
            'exams' => $paginatedExams,
            'vesselGroups' => $vesselGroups,
            'vessels' => $vessels,
            'search' => $search,
            'vesselName' => $vesselName,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'reviewStatus' => $reviewStatus,
        ]);
    }

    /**
     * Fold a flat list of PIC rows into an ordered vessel -> batch -> PIC tree.
     * Legacy rows with no upload_batch_id each become their own single-item bucket so
     * they still render (and stay individually reviewable) instead of being dropped.
     *
     * @param  array<int,array<string,mixed>>  $exams
     * @return array<int,array<string,mixed>>
     */
    private function groupExamsByVesselAndBatch(array $exams): array
    {
        $vessels = [];

        foreach ($exams as $exam) {
            $vesselName = $exam['vessel_name'] ?? 'Unknown Vessel';
            $rawBatch = $exam['upload_batch_id'] ?? null;
            $hasBatch = $rawBatch !== null && $rawBatch !== '';

            if (!isset($vessels[$vesselName])) {
                $vessels[$vesselName] = [
                    'vessel_name' => $vesselName,
                    'pic_count' => 0,
                    'batches' => [],
                ];
            }
            $vessels[$vesselName]['pic_count']++;

            // Legacy (batch-less) rows are keyed per row so they never merge together.
            $bucketKey = $hasBatch
                ? 'batch:' . $rawBatch
                : 'legacy:' . ($exam['id'] ?? uniqid('', true));

            if (!isset($vessels[$vesselName]['batches'][$bucketKey])) {
                $vessels[$vesselName]['batches'][$bucketKey] = [
                    'batch_id' => $hasBatch ? $rawBatch : null,
                    'is_legacy' => !$hasBatch,
                    'submitted_date' => $exam['submitted_date'] ?? null,
                    'submitted_by' => $exam['submitted_by'] ?? null,
                    'items' => [],
                ];
            }
            $vessels[$vesselName]['batches'][$bucketKey]['items'][] = $exam;
        }

        // Collapse the associative keys to plain lists and derive one review status per
        // batch so the view can render a single status pill without re-scanning rows.
        $result = [];
        foreach ($vessels as $vessel) {
            $batches = [];
            foreach ($vessel['batches'] as $batch) {
                $batch['pic_count'] = count($batch['items']);
                $batch['status'] = $this->rollupBatchStatus($batch['items']);
                $batches[] = $batch;
            }
            $vessel['batches'] = $batches;
            $result[] = $vessel;
        }

        return $result;
    }

    /**
     * Reduce a batch's PIC review states to one label:
     * all approved => approved, all rejected => rejected, any still pending => pending,
     * otherwise a decided-but-split batch => mixed.
     *
     * @param  array<int,array<string,mixed>>  $items
     */
    private function rollupBatchStatus(array $items): string
    {
        $statuses = [];
        foreach ($items as $item) {
            $statuses[$item['review_status'] ?? 'pending'] = true;
        }

        if (isset($statuses['pending'])) {
            return 'pending';
        }
        if (count($statuses) === 1) {
            return array_key_first($statuses); // approved or rejected
        }

        return 'mixed';
    }

    public function approve(int $id)
    {
        $result = $this->api->reviewExam($id, 'approve', session('user_name', 'Unknown'));

        if ($result['success'] ?? false) {
            return back()->with('success', 'Exam approved.');
        }

        return back()->with('error', $this->reviewErrorMessage($result));
    }

    public function reject(int $id, Request $request)
    {
        $request->validate(['remarks' => 'required|string|max:1000']);

        $result = $this->api->reviewExam($id, 'reject', session('user_name', 'Unknown'), $request->input('remarks'));

        if ($result['success'] ?? false) {
            return back()->with('success', 'Exam rejected.');
        }

        return back()->with('error', $this->reviewErrorMessage($result));
    }

    public function approveBatch(string $batch)
    {
        $result = $this->api->reviewExamBatch($batch, 'approve', session('user_name', 'Unknown'));

        if ($result['success'] ?? false) {
            $updated = $result['data']['updated'] ?? null;
            return back()->with('success', $updated ? "Approved {$updated} exam(s) in this upload." : 'Upload approved.');
        }

        return back()->with('error', $this->reviewErrorMessage($result));
    }

    public function rejectBatch(string $batch, Request $request)
    {
        $request->validate(['remarks' => 'required|string|max:1000']);

        $result = $this->api->reviewExamBatch($batch, 'reject', session('user_name', 'Unknown'), $request->input('remarks'));

        if ($result['success'] ?? false) {
            $updated = $result['data']['updated'] ?? null;
            return back()->with('success', $updated ? "Rejected {$updated} exam(s) in this upload." : 'Upload rejected.');
        }

        return back()->with('error', $this->reviewErrorMessage($result));
    }

    private function reviewErrorMessage(array $result): string
    {
        return $result['data']['message']
            ?? $result['error']
            ?? 'Unable to update review status (status: ' . ($result['status'] ?? 'unknown') . ').';
    }

    public function show(int $id)
    {
        $result = $this->api->getExam($id);
        $exam = $result['data']['data'] ?? $result['data'] ?? null;

        if (!$exam) {
            return redirect()->route('exams.index')->with('error', 'Exam not found.');
        }

        // Process answers to add question text from QuestionsHelper
        $answers = $exam['answers'] ?? [];
        
        // If answers is a string, decode it
        if (is_string($answers)) {
            $answers = json_decode($answers, true);
        }
        
        $processedAnswers = [];
        $yesCount = 0;
        $noCount = 0;
        $notAnsweredCount = 0;
        
        if (is_array($answers) && !empty($answers)) {
            foreach ($answers as $key => $value) {
                // Handle different answer formats
                if (is_array($value)) {
                    $answerValue = $value['answer'] ?? $value['value'] ?? $value['status'] ?? 'Not answered';
                    $remarks = $value['remarks'] ?? $value['comment'] ?? null;
                } else {
                    $answerValue = $value;
                    $remarks = null;
                }
                
                // Get question text from helper
                $questionText = \App\Helpers\QuestionsHelper::getQuestionText($key);
                
                // If question not found, use formatted key
                if (!$questionText) {
                    $questionText = "Question: {$key}";
                }
                
                // Get category
                $questionData = \App\Helpers\QuestionsHelper::getQuestion($key);
                $category = $questionData['category'] ?? 'General';
                
                // Count statistics
                $answerUpper = strtoupper(trim($answerValue));
                if ($answerUpper === 'YES') {
                    $yesCount++;
                } elseif ($answerUpper === 'NO') {
                    $noCount++;
                } elseif (empty($answerValue) || $answerUpper === 'NOT ANSWERED') {
                    $notAnsweredCount++;
                }
                
                $processedAnswers[] = [
                    'key' => $key,
                    'question_text' => $questionText,
                    'category' => $category,
                    'answer' => $answerValue,
                    'remarks' => $remarks,
                    'status_class' => $this->getAnswerStatusClass($answerValue),
                    'status_badge' => $this->getAnswerStatusBadge($answerValue)
                ];
            }
        }
        
        $exam['processed_answers'] = $processedAnswers;
        $exam['statistics'] = [
            'yes_count' => $yesCount,
            'no_count' => $noCount,
            'not_answered_count' => $notAnsweredCount,
            'total_items' => count($processedAnswers),
            'completion_rate' => count($processedAnswers) > 0 
                ? round((($yesCount + $noCount) / count($processedAnswers)) * 100, 2)
                : 0
        ];

        return view('exams.show', compact('exam'));
    }

    private function getAnswerStatusClass($answer): string
    {
        $answerUpper = strtoupper(trim($answer));
        if ($answerUpper === 'YES') {
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300';
        } elseif ($answerUpper === 'NO') {
            return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300';
        } else {
            return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
        }
    }

    private function getAnswerStatusBadge($answer): string
    {
        $answerUpper = strtoupper(trim($answer));
        if ($answerUpper === 'YES') {
            return '<i class="ti ti-check"></i> YES';
        } elseif ($answerUpper === 'NO') {
            return '<i class="ti ti-x"></i> NO';
        } else {
            return '<i class="ti ti-question-mark"></i> Not Answered';
        }
    }

    public function exportExcel(Request $request)
    {
        $result = $this->api->getExams();
        $exams = $result['data']['data'] ?? $result['data'] ?? [];
        if (!is_array($exams)) $exams = [];

        // Sort by date newest first for export
        usort($exams, function($a, $b) {
            return strtotime($b['submitted_date'] ?? '1970-01-01') - strtotime($a['submitted_date'] ?? '1970-01-01');
        });

        return Excel::download(new ExamsExport($exams), 'exams-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $result = $this->api->getExams();
        $exams = $result['data']['data'] ?? $result['data'] ?? [];
        if (!is_array($exams)) $exams = [];

        // Sort by date newest first for PDF
        usort($exams, function($a, $b) {
            return strtotime($b['submitted_date'] ?? '1970-01-01') - strtotime($a['submitted_date'] ?? '1970-01-01');
        });

        $pdf = Pdf::loadView('exports.exams-pdf', compact('exams'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('exams-' . now()->format('Y-m-d') . '.pdf');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,xlsx,xls|max:10240']);

        try {
            // Read uploaded file and push to API
            $rows = Excel::toArray([], $request->file('file'));
            $imported = 0;
            if (!empty($rows[0])) {
                $headers = array_map('strtolower', $rows[0][0] ?? []);
                foreach (array_slice($rows[0], 1) as $row) {
                    $data = array_combine($headers, $row);
                    $apiResult = $this->api->createExam([
                        'exam_id' => $data['exam_id'] ?? '',
                        'vessel_name' => $data['vessel_name'] ?? '',
                        'person_in_charge' => $data['person_in_charge'] ?? '',
                        'submitted_by' => $data['submitted_by'] ?? '',
                        'email' => $data['email'] ?? '',
                        'submitted_date' => $data['submitted_date'] ?? now()->toDateTimeString(),
                        'answers' => json_decode($data['answers'] ?? '{}', true) ?? [],
                    ]);
                    if ($apiResult['success']) $imported++;
                }
            }
            return back()->with('success', "Imported {$imported} exams successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}