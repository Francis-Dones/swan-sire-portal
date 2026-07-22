<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Cache database results for 5 minutes to improve loading speed
            $exams = Cache::remember('dashboard_exams', 300, function() {
                return Exam::all()->toArray();
            });
            
            if (!is_array($exams)) $exams = [];
        } catch (\Exception $e) {
            Log::error('Dashboard Database Error: ' . $e->getMessage());
            $exams = [];
            
            return view('dashboard.index', [
                'totalExams' => 0,
                'totalVessels' => 0,
                'recentExams' => [],
                'monthlyData' => [],
                'inspectionTypes' => [],
                'vessels' => [],
                'yesNoStatistics' => [],
                'apiError' => 'Unable to load inspection data. Please try again later.'
            ]);
        }

        $totalExams = count($exams);
        $vessels = collect($exams)->pluck('vessel_name')->unique()->filter()->values();
        $totalVessels = $vessels->count();

        // Recent exams (last 5)
        $recentExams = collect($exams)->sortByDesc('submitted_date')->take(5)->values()->toArray();

        // Monthly chart data
        $monthlyData = $this->buildMonthlyData($exams);

        // Inspection type distribution
        $inspectionTypes = $this->buildInspectionTypes($exams);

        // YES/NO Statistics for each exam
        $yesNoStatistics = $this->buildYesNoStatistics($exams);

        return view('dashboard.index', compact(
            'totalExams', 'totalVessels', 'recentExams',
            'monthlyData', 'inspectionTypes', 'vessels',
            'yesNoStatistics'
        ));
    }

    /**
     * Build YES/NO statistics for each exam
     * Uses the answers array from Exam model (automatically cast from JSON)
     */
    private function buildYesNoStatistics(array $exams): array
    {
        $statistics = [];
        
        foreach ($exams as $exam) {
            $examId = $exam['exam_id'] ?? $exam['id'] ?? null;
            $vesselName = $exam['vessel_name'] ?? 'Unknown Vessel';
            $submittedDate = $exam['submitted_date'] ?? 'Unknown Date';
            
            // Format date if it's a Carbon object or string
            if ($submittedDate instanceof \DateTime) {
                $submittedDate = $submittedDate->format('Y-m-d H:i:s');
            }
            
            // Get the answers - handle both array and string cases safely
            $answers = $exam['answers'] ?? [];
            
            // SAFE HANDLING: If answers is a string, try to decode it
            if (is_string($answers)) {
                $decoded = json_decode($answers, true);
                if (is_array($decoded)) {
                    $answers = $decoded;
                } else {
                    // If it's not valid JSON, treat as empty array
                    $answers = [];
                    Log::warning("Invalid JSON in answers for exam {$examId}");
                }
            }
            
            // Ensure answers is always an array
            if (!is_array($answers)) {
                $answers = [];
            }
            
            $yesCount = 0;
            $noCount = 0;
            $notAnsweredCount = 0;
            $noRemarks = [];
            $notAnsweredItems = [];
            
            if (!empty($answers)) {
                foreach ($answers as $key => $answer) {
                    // SAFE HANDLING: Skip if answer is not an array or scalar
                    if (!is_array($answer) && !is_string($answer) && !is_numeric($answer)) {
                        continue;
                    }
                    
                    // If answer is a scalar value (string, number), process it directly
                    if (!is_array($answer)) {
                        $answerValue = (string)$answer;
                        $itemNumber = is_numeric($key) ? 'Item ' . ($key + 1) : (string)$key;
                        $remarks = null;
                        
                        $answerUpper = strtoupper(trim($answerValue));
                        
                        if ($answerUpper === 'YES') {
                            $yesCount++;
                        } elseif ($answerUpper === 'NO') {
                            $noCount++;
                            $noRemarks[] = [
                                'item' => $itemNumber,
                                'remarks' => 'No remarks provided'
                            ];
                        } elseif (empty($answerValue) || $answerUpper === 'NOT ANSWERED' || $answerUpper === 'NULL') {
                            $notAnsweredCount++;
                            $notAnsweredItems[] = $itemNumber;
                        } else {
                            // Other values (text answers, etc.)
                            $notAnsweredCount++;
                            $notAnsweredItems[] = $itemNumber . " ({$answerValue})";
                        }
                        continue;
                    }
                    
                    // Determine the answer value based on structure (answer is an array)
                    $answerValue = null;
                    $itemNumber = null;
                    $remarks = null;
                    
                    // Check if answer has the expected keys
                    if (isset($answer['answer'])) {
                        $answerValue = $answer['answer'];
                        $itemNumber = $answer['item_number'] ?? $answer['question_code'] ?? $key;
                        $remarks = $answer['remarks'] ?? null;
                    } 
                    elseif (isset($answer['value'])) {
                        $answerValue = $answer['value'];
                        $itemNumber = $answer['code'] ?? $answer['item_number'] ?? $key;
                        $remarks = $answer['remark'] ?? $answer['remarks'] ?? null;
                    }
                    elseif (isset($answer['status'])) {
                        $answerValue = $answer['status'];
                        $itemNumber = $answer['question_code'] ?? $answer['item_number'] ?? $key;
                        $remarks = $answer['remarks'] ?? null;
                    }
                    // Handle format where answer has a single key (like '4.3.2' => 'YES')
                    else {
                        $arrayKeys = array_keys($answer);
                        if (count($arrayKeys) === 1 && !is_numeric($arrayKeys[0])) {
                            $itemNumber = $arrayKeys[0];
                            $answerValue = $answer[$arrayKeys[0]];
                            $remarks = null;
                        } else {
                            // If structure is unknown, use the key as item number
                            $itemNumber = is_numeric($key) ? 'Item ' . ($key + 1) : (string)$key;
                            $answerValue = null;
                        }
                    }
                    
                    // Process the answer value
                    if ($answerValue !== null && is_string($answerValue)) {
                        $answerUpper = strtoupper(trim($answerValue));
                        
                        if ($answerUpper === 'YES') {
                            $yesCount++;
                        } 
                        elseif ($answerUpper === 'NO') {
                            $noCount++;
                            $noRemarks[] = [
                                'item' => $itemNumber,
                                'remarks' => $remarks && !empty($remarks) ? $remarks : 'No remarks provided'
                            ];
                        } 
                        elseif ($answerUpper === '' || $answerUpper === 'NOT ANSWERED' || $answerUpper === 'NULL' || $answerUpper === 'N/A') {
                            $notAnsweredCount++;
                            $notAnsweredItems[] = $itemNumber;
                        }
                        else {
                            // Any other value - could be considered answered but not YES/NO
                            $notAnsweredCount++;
                            $notAnsweredItems[] = $itemNumber . " ({$answerValue})";
                        }
                    } 
                    else {
                        // Null or non-string answer
                        $notAnsweredCount++;
                        $notAnsweredItems[] = $itemNumber;
                    }
                }
            }
            
            $totalItemsCount = $yesCount + $noCount + $notAnsweredCount;
            
            $statistics[] = [
                'exam_id' => $examId,
                'vessel_name' => $vesselName,
                'submitted_date' => $submittedDate,
                'yes_count' => $yesCount,
                'no_count' => $noCount,
                'not_answered_count' => $notAnsweredCount,
                'total_items' => $totalItemsCount,
                'no_remarks' => $noRemarks,
                'not_answered_items' => $notAnsweredItems,
                'completion_rate' => $totalItemsCount > 0 
                    ? round((($yesCount + $noCount) / $totalItemsCount) * 100, 2)
                    : 0
            ];
        }
        
        return $statistics;
    }

    /**
     * Get summary statistics across all exams (AJAX endpoint)
     */
    public function getSummaryStatistics()
    {
        try {
            $exams = Cache::remember('dashboard_exams_summary', 300, function() {
                return Exam::all()->toArray();
            });
            
            if (!is_array($exams)) $exams = [];
            
            $statistics = $this->buildYesNoStatistics($exams);
            
            $totalYes = array_sum(array_column($statistics, 'yes_count'));
            $totalNo = array_sum(array_column($statistics, 'no_count'));
            $totalNotAnswered = array_sum(array_column($statistics, 'not_answered_count'));
            $totalItems = $totalYes + $totalNo + $totalNotAnswered;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'per_exam' => $statistics,
                    'summary' => [
                        'total_yes' => $totalYes,
                        'total_no' => $totalNo,
                        'total_not_answered' => $totalNotAnswered,
                        'total_items' => $totalItems,
                        'yes_percentage' => $totalItems > 0 ? round(($totalYes / $totalItems) * 100, 2) : 0,
                        'no_percentage' => $totalItems > 0 ? round(($totalNo / $totalItems) * 100, 2) : 0,
                        'not_answered_percentage' => $totalItems > 0 ? round(($totalNotAnswered / $totalItems) * 100, 2) : 0
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Summary Statistics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug method to check answers structure (remove in production)
     */
    public function debugAnswers()
    {
        try {
            $exams = Exam::all();
            
            $debug = [];
            foreach ($exams as $exam) {
                $answers = $exam->answers;
                $debug[] = [
                    'exam_id' => $exam->exam_id,
                    'vessel_name' => $exam->vessel_name,
                    'answers_type' => gettype($answers),
                    'answers_is_array' => is_array($answers),
                    'answers_is_string' => is_string($answers),
                    'answers_preview' => is_array($answers) 
                        ? array_slice($answers, 0, 3) 
                        : (is_string($answers) ? substr($answers, 0, 500) : null),
                    'total_answers' => is_array($answers) ? count($answers) : (is_string($answers) ? strlen($answers) : 0)
                ];
            }
            
            return response()->json([
                'success' => true,
                'total_exams' => count($debug),
                'data' => $debug
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function buildMonthlyData(array $exams): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->format('M Y');
            $count = collect($exams)->filter(function ($exam) use ($key) {
                $date = $exam['submitted_date'] ?? '';
                if ($date instanceof \DateTime) {
                    $date = $date->format('Y-m-d');
                }
                if (is_string($date)) {
                    return str_starts_with($date, $key);
                }
                return false;
            })->count();
            $months[] = ['label' => $label, 'count' => $count];
        }
        return $months;
    }

    private function buildInspectionTypes(array $exams): array
    {
        $grouped = collect($exams)->groupBy('vessel_name')->map->count();
        return $grouped->take(6)->toArray();
    }
}