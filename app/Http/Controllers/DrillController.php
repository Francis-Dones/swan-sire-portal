<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DrillController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Canonical SIRE drill ranks and how each selects its question pool (DRILL_SPEC §7).
     * A question is in a rank's pool when its `ranks` intersect the rank's `match` list
     * OR its `ranks` include 'All Crew' (available to everyone). The same rule is applied
     * client-side by the Alpine quiz — one source of truth for the matching logic.
     *
     * @return array<int,array{key:string,label:string,match:array<int,string>}>
     */
    public static function ranks(): array
    {
        return [
            ['key' => 'Master',            'label' => 'Master',            'match' => ['Master']],
            ['key' => 'Chief Officer',     'label' => 'Chief Officer',     'match' => ['Chief Officer']],
            ['key' => 'Chief Engineer',    'label' => 'Chief Engineer',    'match' => ['Chief Engineer']],
            ['key' => 'Deck Officers',     'label' => 'Deck Officers (2/O, 3/O)', 'match' => ['Deck Officers', 'Officers (General)']],
            ['key' => 'Engineers',         'label' => 'Engineers (2/E, 3/E)',     'match' => ['Engineers']],
            ['key' => 'Ratings/All Crew',  'label' => 'Ratings / All Crew', 'match' => ['All Crew']],
        ];
    }

    /**
     * Read the validated 273-entry question bank from resources/data.
     *
     * @return array<int,array<string,mixed>>
     */
    private function questionBank(): array
    {
        $path = resource_path('data/drill_questions.json');
        if (!is_file($path)) {
            return [];
        }
        $data = json_decode((string) file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Number of questions in a rank's pool (same rule as ranks(), used for the
     * "show pool size" label). Kept server-side so the label is authoritative.
     *
     * @param  array<int,array<string,mixed>>  $bank
     * @param  array<int,string>  $match
     */
    private function poolSize(array $bank, array $match): int
    {
        $count = 0;
        foreach ($bank as $q) {
            $qRanks = $q['ranks'] ?? [];
            if (!is_array($qRanks)) {
                continue;
            }
            if (in_array('All Crew', $qRanks, true) || array_intersect($match, $qRanks)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Part A — the web quiz. The full bank is handed to the Alpine component, which
     * samples 10 questions for the chosen rank, builds one MCQ each, and keeps every
     * answer hidden until the end (DRILL_SPEC §3, §4).
     */
    public function take()
    {
        $bank = $this->questionBank();

        $ranks = array_map(function ($r) use ($bank) {
            $r['pool'] = $this->poolSize($bank, $r['match']);
            return $r;
        }, self::ranks());

        // Distinct vessel names the caller may see (scoped server-side). Shore is not
        // bound to one vessel, so this seeds a picker that also allows free entry.
        $vesselsResult = $this->api->getVessels();
        $vessels = $vesselsResult['data']['data'] ?? $vesselsResult['data'] ?? [];
        if (!is_array($vessels)) {
            $vessels = [];
        }
        $vessels = collect($vessels)->filter()->unique()->sort()->values()->toArray();

        return view('drill.take', [
            'bank' => $bank,
            'ranks' => $ranks,
            'vessels' => $vessels,
            'submittedBy' => session('user_name', 'Unknown'),
        ]);
    }

    /**
     * Receive one completed session from the quiz (a JSON payload in a hidden field),
     * stamp it with the portal user + submit time, and forward it to the API.
     */
    public function submit(Request $request)
    {
        $payload = json_decode((string) $request->input('payload'), true);

        if (!is_array($payload) || empty($payload['vessel_name']) || empty($payload['rank'])
            || !isset($payload['answers']) || !is_array($payload['answers']) || count($payload['answers']) === 0) {
            return back()->with('error', 'The drill result was incomplete and could not be submitted.');
        }

        $answers = [];
        foreach ($payload['answers'] as $a) {
            $answers[] = [
                'question_id'   => (string) ($a['question_id'] ?? ''),
                'question_type' => ($a['question_type'] ?? '') === 'requirement' ? 'requirement' : 'topic',
                'chosen_text'   => (string) ($a['chosen_text'] ?? ''),
                'correct_text'  => (string) ($a['correct_text'] ?? ''),
                'is_correct'    => (bool) ($a['is_correct'] ?? false),
            ];
        }

        $data = [
            'vessel_name'    => (string) $payload['vessel_name'],
            'rank'           => (string) $payload['rank'],
            'submitted_by'   => session('user_name', 'Unknown'),
            'email'          => session('user_email', '') ?: null,
            'score'          => (int) ($payload['score'] ?? 0),
            'total'          => (int) ($payload['total'] ?? count($answers)),
            'answers'        => $answers,
            'submitted_date' => now()->toIso8601String(),
        ];

        $result = $this->api->submitDrill(array_filter($data, fn ($v) => $v !== null));

        if ($result['success'] ?? false) {
            $saved = $result['data']['data'] ?? [];
            $pct = $saved['percentage'] ?? round(($data['score'] / max($data['total'], 1)) * 100);
            return redirect()->route('drill.results')
                ->with('success', "Drill result submitted — {$data['score']}/{$data['total']} ({$pct}%).");
        }

        return back()->with('error', $this->apiErrorMessage($result));
    }

    /**
     * Part B — shore list of submitted results (crew mobile + web). The list payload
     * omits per-question answers; those load on the detail page.
     */
    public function results(Request $request)
    {
        $vesselName  = $request->get('vessel_name');
        $rank        = $request->get('rank');
        $submittedBy = $request->get('submitted_by');
        $dateFrom    = $request->get('date_from');
        $dateTo      = $request->get('date_to');
        $perPage     = 20;
        $currentPage = (int) $request->get('page', 1);

        $result = $this->api->getDrillSubmissions(array_filter([
            'vessel_name'  => $vesselName,
            'rank'         => $rank,
            'submitted_by' => $submittedBy,
            'date_from'    => $dateFrom,
            'date_to'      => $dateTo,
            'page'         => $currentPage,
            'per_page'     => $perPage,
        ], fn ($v) => $v !== null && $v !== ''));

        $submissions = $result['data']['data'] ?? $result['data'] ?? [];
        if (!is_array($submissions)) {
            $submissions = [];
        }
        $meta = $result['data']['meta'] ?? [];

        $paginated = new LengthAwarePaginator(
            collect($submissions)->values(),
            $meta['total'] ?? count($submissions),
            $meta['per_page'] ?? $perPage,
            $meta['current_page'] ?? $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $vesselsResult = $this->api->getVessels();
        $vessels = $vesselsResult['data']['data'] ?? $vesselsResult['data'] ?? [];
        if (!is_array($vessels)) {
            $vessels = [];
        }
        $vessels = collect($vessels)->filter()->unique()->sort()->values()->toArray();

        return view('drill.results', [
            'submissions' => $paginated,
            'vessels'     => $vessels,
            'ranks'       => self::ranks(),
            'vesselName'  => $vesselName,
            'rank'        => $rank,
            'submittedBy' => $submittedBy,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
        ]);
    }

    /**
     * One submission with its full per-question answers.
     */
    public function show(int $id)
    {
        $result = $this->api->getDrillSubmission($id);
        $submission = $result['data']['data'] ?? $result['data'] ?? null;

        if (!is_array($submission) || empty($submission)) {
            return redirect()->route('drill.results')->with('error', 'Drill submission not found.');
        }

        $answers = $submission['answers'] ?? [];
        if (is_string($answers)) {
            $answers = json_decode($answers, true) ?: [];
        }
        $submission['answers'] = is_array($answers) ? $answers : [];

        return view('drill.show', ['submission' => $submission]);
    }

    private function apiErrorMessage(array $result): string
    {
        return $result['data']['message']
            ?? $result['error']
            ?? 'Unable to submit the drill result (status: ' . ($result['status'] ?? 'unknown') . ').';
    }
}
