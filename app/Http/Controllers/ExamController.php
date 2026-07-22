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

        $result = $this->api->getExams();
        $allExams = $result['data']['data'] ?? $result['data'] ?? [];
        if (!is_array($allExams)) $allExams = [];

        // Client-side filtering since API may not support all filters
        $examsCollection = collect($allExams);

        // SORT BY DATE - NEWEST FIRST (descending order)
        $examsCollection = $examsCollection->sortByDesc(function ($exam) {
            return strtotime($exam['submitted_date'] ?? '1970-01-01');
        })->values();

        if ($search) {
            $examsCollection = $examsCollection->filter(function ($e) use ($search) {
                $s = strtolower($search);
                return str_contains(strtolower($e['exam_id'] ?? ''), $s)
                    || str_contains(strtolower($e['vessel_name'] ?? ''), $s)
                    || str_contains(strtolower($e['submitted_by'] ?? ''), $s)
                    || str_contains(strtolower($e['person_in_charge'] ?? ''), $s);
            });
        }

        if ($vesselName) {
            $examsCollection = $examsCollection->filter(fn($e) => stripos($e['vessel_name'] ?? '', $vesselName) !== false);
        }

        if ($dateFrom) {
            $examsCollection = $examsCollection->filter(fn($e) => ($e['submitted_date'] ?? '') >= $dateFrom);
        }

        if ($dateTo) {
            $examsCollection = $examsCollection->filter(fn($e) => ($e['submitted_date'] ?? '') <= $dateTo . ' 23:59:59');
        }

        $vessels = collect($allExams)->pluck('vessel_name')->unique()->filter()->sort()->values()->toArray();
        
        // Paginate the filtered results (20 per page)
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $examsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedExams = new LengthAwarePaginator(
            $currentPageItems,
            $examsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('exams.index', [
            'exams' => $paginatedExams,
            'vessels' => $vessels,
            'search' => $search,
            'vesselName' => $vesselName,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
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