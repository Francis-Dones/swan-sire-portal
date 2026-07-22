<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VesselsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class VesselController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        // Debug: Check if API is working
        $result = $this->api->getExams();
        
        // Debug: Log the result to see what's coming
        \Log::info('API Response:', $result);
        
        // Try different data structures
        $exams = [];
        
        if (isset($result['data']['data']) && is_array($result['data']['data'])) {
            $exams = $result['data']['data'];
        } elseif (isset($result['data']) && is_array($result['data'])) {
            $exams = $result['data'];
        } elseif (is_array($result)) {
            $exams = $result;
        }
        
        // Debug: Check if exams have data
        \Log::info('Exams count: ' . count($exams));
        
        if (empty($exams)) {
            // If no exams, return empty vessels
            $vessels = [];
            $search = $request->get('search');
            return view('vessels.index', compact('vessels', 'search'));
        }

        // Group exams by vessel
        $vessels = collect($exams)
            ->filter(function($exam) {
                // Filter out exams without vessel_name
                return !empty($exam['vessel_name'] ?? null);
            })
            ->groupBy('vessel_name')
            ->map(function ($vesselExams, $name) {
                // Get all persons in charge
                $persons = $vesselExams
                    ->pluck('person_in_charge')
                    ->unique()
                    ->filter()
                    ->values()
                    ->toArray();
                
                // Get last inspection date
                $lastInspection = $vesselExams->max('submitted_date');
                
                // Get vessel ID from first exam
                $vesselId = $vesselExams->first()['vessel_id'] ?? null;
                
                return [
                    'vessel_name' => $name,
                    'vessel_id' => $vesselId,
                    'total_exams' => $vesselExams->count(),
                    'last_inspection' => $lastInspection,
                    'persons' => $persons,
                    'exams' => $vesselExams->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();

        // Apply search filter
        $search = $request->get('search');
        if ($search) {
            $vessels = array_filter($vessels, function($v) use ($search) {
                return stripos($v['vessel_name'], $search) !== false;
            });
            $vessels = array_values($vessels);
        }

        // Debug: Check vessels count
        \Log::info('Vessels count: ' . count($vessels));

        return view('vessels.index', compact('vessels', 'search'));
    }

    public function show(string $vesselName)
    {
        // Decode URL encoded vessel name
        $vesselName = urldecode($vesselName);
        
        // Get exams for specific vessel
        $result = $this->api->getExamsByVessel($vesselName);
        
        // Debug: Log the result
        \Log::info('Vessel Show API Response for ' . $vesselName . ':', $result);
        
        // Try different data structures
        $exams = [];
        
        if (isset($result['data']['data']) && is_array($result['data']['data'])) {
            $exams = $result['data']['data'];
        } elseif (isset($result['data']) && is_array($result['data'])) {
            $exams = $result['data'];
        } elseif (is_array($result)) {
            $exams = $result;
        }
        
        // If no exams from specific endpoint, filter from all exams
        if (empty($exams)) {
            $allExamsResult = $this->api->getExams();
            $allExams = [];
            
            if (isset($allExamsResult['data']['data']) && is_array($allExamsResult['data']['data'])) {
                $allExams = $allExamsResult['data']['data'];
            } elseif (isset($allExamsResult['data']) && is_array($allExamsResult['data'])) {
                $allExams = $allExamsResult['data'];
            } elseif (is_array($allExamsResult)) {
                $allExams = $allExamsResult;
            }
            
            // Filter exams by vessel name
            $exams = array_filter($allExams, function($exam) use ($vesselName) {
                return ($exam['vessel_name'] ?? '') === $vesselName;
            });
            $exams = array_values($exams);
        }
        
        // Debug: Log exams count
        \Log::info('Exams count for ' . $vesselName . ': ' . count($exams));

        // Build vessel summary data
        $vessel = [];
        if (!empty($exams)) {
            $firstExam = $exams[0];
            $persons = collect($exams)
                ->pluck('person_in_charge')
                ->unique()
                ->filter()
                ->values()
                ->toArray();
            
            $vessel = [
                'vessel_id' => $firstExam['vessel_id'] ?? 'N/A',
                'registered_date' => $firstExam['registered_date'] ?? null,
                'last_inspection' => collect($exams)->max('submitted_date'),
                'persons' => $persons,
                'total_exams' => count($exams),
                'total_answers' => collect($exams)->sum(function($exam) {
                    return is_array($exam['answers'] ?? null) ? count($exam['answers']) : 0;
                }),
                'unique_inspectors' => collect($exams)->pluck('submitted_by')->unique()->count(),
            ];
        }

        return view('vessels.show', compact('vesselName', 'vessel', 'exams'));
    }

    public function exportExcel(Request $request)
    {
        $result = $this->api->getExams();
        $exams = $result['data']['data'] ?? $result['data'] ?? [];
        if (!is_array($exams)) $exams = [];

        $vessels = collect($exams)->groupBy('vessel_name')->map(function ($g, $name) {
            return [
                'vessel_name' => $name,
                'total_exams' => $g->count(),
                'last_inspection' => $g->max('submitted_date'),
                'persons' => $g->pluck('person_in_charge')->unique()->filter()->implode(', '),
            ];
        })->values()->toArray();

        return Excel::download(new VesselsExport($vessels), 'vessels-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $result = $this->api->getExams();
        $exams = $result['data']['data'] ?? $result['data'] ?? [];
        if (!is_array($exams)) $exams = [];

        $vessels = collect($exams)->groupBy('vessel_name')->map(function ($g, $name) {
            return [
                'vessel_name' => $name,
                'total_exams' => $g->count(),
                'last_inspection' => $g->max('submitted_date'),
                'persons' => $g->pluck('person_in_charge')->unique()->filter()->implode(', '),
            ];
        })->values()->toArray();

        $pdf = Pdf::loadView('exports.vessels-pdf', compact('vessels'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('vessels-' . now()->format('Y-m-d') . '.pdf');
    }
}