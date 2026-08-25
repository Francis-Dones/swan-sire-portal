@extends('layouts.app')

@section('title', 'Take SIRE Drill')
@section('page-title', 'SIRE Readiness Drill')
@section('page-subtitle', 'Sample 10 questions for a rank — answers revealed at the end')

@section('content')
<div class="max-w-3xl mx-auto" x-data="drillQuiz()" x-cloak>

    {{-- ══ SETUP ══ --}}
    <div x-show="phase === 'setup'" class="space-y-5">
        <div class="card p-5 sm:p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-navy-600 to-cyan-600 flex items-center justify-center shadow-glow-navy flex-shrink-0">
                    <i class="ti ti-clipboard-check text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-lg leading-tight">Start a drill</h2>
                    <p class="text-sm text-slate-400 dark:text-slate-500">Pick the vessel and the rank being drilled.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Vessel</label>
                    <input list="drill-vessels" x-model="vesselName" placeholder="Select or type a vessel"
                           class="form-input" autocomplete="off">
                    <datalist id="drill-vessels">
                        @foreach($vessels as $v)
                        <option value="{{ $v }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="form-label">Rank / Person-in-charge</label>
                    <select x-model="rankKey" class="form-input cursor-pointer">
                        <option value="">Select a rank…</option>
                        <template x-for="r in ranks" :key="r.key">
                            <option :value="r.key" x-text="r.label + ' — ' + r.pool + ' in pool'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="mt-5 flex items-center gap-3 rounded-xl bg-navy-50 dark:bg-navy-900/40 border border-navy-100 dark:border-navy-800 px-4 py-3">
                <i class="ti ti-info-circle text-navy-500 dark:text-navy-300 text-lg flex-shrink-0"></i>
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    You will get <strong>10 random questions</strong> for the chosen rank. Correct answers stay hidden until you finish.
                </p>
            </div>

            <p x-show="setupError" x-text="setupError" class="mt-3 text-sm text-rose-600 dark:text-rose-400"></p>

            <button type="button" @click="start()" class="btn btn-primary w-full mt-5">
                <i class="ti ti-player-play"></i> Start Drill
            </button>
        </div>
    </div>

    {{-- ══ QUIZ ══ --}}
    <div x-show="phase === 'quiz'" class="space-y-5">
        <div class="card p-5 sm:p-6">
            {{-- header: vessel/rank + progress --}}
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="pill pill-info"><i class="ti ti-ship"></i> <span x-text="vesselName"></span></span>
                    <span class="pill pill-info"><i class="ti ti-user"></i> <span x-text="rankKey"></span></span>
                </div>
                <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 tnum">
                    Question <span x-text="index + 1"></span> / <span x-text="questions.length"></span>
                </span>
            </div>

            {{-- progress bar --}}
            <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-navy-800 overflow-hidden mb-5">
                <div class="h-full bg-gradient-to-r from-navy-600 to-cyan-500 rounded-full transition-all duration-300"
                     :style="`width: ${((index + 1) / questions.length) * 100}%`"></div>
            </div>

            <template x-if="current">
                <div>
                    <span class="inline-block text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mb-3"
                          :class="current.question_type === 'requirement' ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300' : 'bg-navy-100 text-navy-700 dark:bg-navy-900/40 dark:text-navy-300'"
                          x-text="current.question_type === 'requirement' ? 'Verify the requirement' : 'Identify the topic'"></span>

                    <p class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white leading-snug mb-5" x-text="current.stem"></p>

                    <div class="space-y-2.5">
                        <template x-for="(opt, oi) in current.options" :key="oi">
                            <button type="button" @click="current.chosen_text = opt"
                                    class="w-full text-left flex items-center gap-3 px-4 py-3 rounded-xl border transition-all"
                                    :class="current.chosen_text === opt
                                        ? 'border-navy-500 bg-navy-50 dark:bg-navy-900/50 dark:border-navy-400 ring-2 ring-navy-500/20'
                                        : 'border-slate-200 dark:border-navy-800 hover:border-navy-300 dark:hover:border-navy-600 hover:bg-slate-50 dark:hover:bg-navy-900/40'">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 border"
                                      :class="current.chosen_text === opt
                                        ? 'bg-navy-600 border-navy-600 text-white'
                                        : 'border-slate-300 dark:border-navy-700 text-slate-500 dark:text-slate-400'"
                                      x-text="String.fromCharCode(65 + oi)"></span>
                                <span class="text-sm text-slate-700 dark:text-slate-200" x-text="opt"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- nav --}}
            <div class="flex items-center justify-between gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-navy-800">
                <button type="button" @click="prev()" :disabled="index === 0"
                        class="btn btn-outline disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="ti ti-arrow-left"></i> Back
                </button>
                <span class="text-xs text-slate-400 tnum" x-text="answeredCount + ' / ' + questions.length + ' answered'"></span>
                <template x-if="index < questions.length - 1">
                    <button type="button" @click="next()" :disabled="!current || !current.chosen_text"
                            class="btn btn-primary disabled:opacity-40 disabled:cursor-not-allowed">
                        Next <i class="ti ti-arrow-right"></i>
                    </button>
                </template>
                <template x-if="index === questions.length - 1">
                    <button type="button" @click="finish()" :disabled="answeredCount < questions.length"
                            class="btn btn-primary disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="ti ti-flag-check"></i> Finish
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- ══ RESULT ══ --}}
    <div x-show="phase === 'result'" class="space-y-5">
        <div class="card p-6 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center mb-4"
                 :class="scoreBand === 'good' ? 'bg-emerald-100 dark:bg-emerald-900/40' : (scoreBand === 'mid' ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-rose-100 dark:bg-rose-900/40')">
                <i class="ti text-3xl"
                   :class="scoreBand === 'good' ? 'ti-trophy text-emerald-600 dark:text-emerald-400' : (scoreBand === 'mid' ? 'ti-mood-check text-amber-600 dark:text-amber-400' : 'ti-refresh text-rose-600 dark:text-rose-400')"></i>
            </div>
            <p class="text-sm text-slate-400 dark:text-slate-500">Your score</p>
            <p class="text-4xl font-extrabold text-slate-900 dark:text-white tnum mt-1">
                <span x-text="score"></span><span class="text-slate-300 dark:text-slate-600">/</span><span x-text="questions.length"></span>
            </p>
            <span class="pill mt-3"
                  :class="scoreBand === 'good' ? 'pill-approved' : (scoreBand === 'mid' ? 'pill-pending' : 'pill-rejected')"
                  x-text="percentage + '%'"></span>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-3">
                <span x-text="vesselName"></span> · <span x-text="rankKey"></span>
            </p>
        </div>

        {{-- review --}}
        <div class="space-y-3">
            <h3 class="font-bold text-slate-800 dark:text-slate-100 px-1">Review</h3>
            <template x-for="(q, qi) in questions" :key="qi">
                <div class="card p-4"
                     :class="q.chosen_text === q.correct_text ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-rose-500'">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 leading-snug" x-text="(qi + 1) + '. ' + q.stem"></p>
                        <span class="pill flex-shrink-0"
                              :class="q.chosen_text === q.correct_text ? 'pill-approved' : 'pill-rejected'"
                              x-text="q.chosen_text === q.correct_text ? 'Correct' : 'Wrong'"></span>
                    </div>
                    <div class="mt-3 space-y-1.5 text-sm">
                        <p class="flex items-start gap-2">
                            <span class="text-slate-400 dark:text-slate-500 flex-shrink-0">Your answer:</span>
                            <span :class="q.chosen_text === q.correct_text ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-rose-700 dark:text-rose-400 font-medium'"
                                  x-text="q.chosen_text || '— (unanswered)'"></span>
                        </p>
                        <p x-show="q.chosen_text !== q.correct_text" class="flex items-start gap-2">
                            <span class="text-slate-400 dark:text-slate-500 flex-shrink-0">Correct answer:</span>
                            <span class="text-emerald-700 dark:text-emerald-400 font-medium" x-text="q.correct_text"></span>
                        </p>
                    </div>
                </div>
            </template>
        </div>

        {{-- submit --}}
        <form method="POST" action="{{ route('drill.submit') }}" x-ref="submitForm">
            @csrf
            <input type="hidden" name="payload" x-ref="payload">
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" @click="submitResult()" :disabled="submitting" class="btn btn-primary flex-1 disabled:opacity-50">
                    <i class="ti ti-cloud-upload"></i> <span x-text="submitting ? 'Submitting…' : 'Submit Result'"></span>
                </button>
                <button type="button" @click="reset()" class="btn btn-outline flex-1">
                    <i class="ti ti-refresh"></i> Take Another
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function drillQuiz() {
    return {
        bank: @json($bank),
        ranks: @json($ranks),
        phase: 'setup',
        vesselName: '',
        rankKey: '',
        setupError: '',
        questions: [],
        index: 0,
        score: 0,
        submitting: false,

        // ── setup ──
        start() {
            this.setupError = '';
            if (!this.vesselName.trim()) { this.setupError = 'Please select or enter a vessel.'; return; }
            if (!this.rankKey) { this.setupError = 'Please select a rank.'; return; }
            const rank = this.ranks.find(r => r.key === this.rankKey);
            if (!rank) { this.setupError = 'Unknown rank.'; return; }
            const pool = this.poolFor(rank.match);
            if (pool.length === 0) { this.setupError = 'No questions available for this rank.'; return; }
            const sample = this.shuffle(pool.slice()).slice(0, Math.min(10, pool.length));
            this.questions = sample.map(q => this.buildMCQ(q));
            this.index = 0;
            this.phase = 'quiz';
        },

        // Same pooling rule as DrillController::ranks(): rank match OR 'All Crew'.
        poolFor(match) {
            return this.bank.filter(q => {
                const rs = q.ranks || [];
                return rs.includes('All Crew') || match.some(m => rs.includes(m));
            });
        },

        buildMCQ(q) {
            const canRequire = q.fact !== null && q.fact !== undefined && q.fact !== '';
            const type = (canRequire && Math.random() < 0.5) ? 'requirement' : 'topic';
            let stem, correct, distractors;

            if (type === 'requirement') {
                stem = 'According to the SMS, what does the company require for: "' + q.shortText + '"?';
                correct = q.fact;
                distractors = (q.factDistractors || []).slice();
                if (distractors.length < 3) {
                    const others = this.bank
                        .filter(o => o.fact && o.fact !== q.fact && !distractors.includes(o.fact))
                        .map(o => o.fact);
                    for (const f of this.shuffle(others)) {
                        if (distractors.length >= 3) break;
                        distractors.push(f);
                    }
                }
                distractors = this.shuffle(distractors).slice(0, 3);
            } else {
                stem = q.question;
                correct = q.shortText;
                distractors = this.shuffle((q.topicDistractors || []).slice()).slice(0, 3);
            }

            const options = this.shuffle([correct, ...distractors]);
            return {
                question_id: q.no,
                question_type: type,
                stem: stem,
                options: options,
                correct_text: correct,
                chosen_text: null,
            };
        },

        // ── quiz ──
        get current() { return this.questions[this.index] || null; },
        get answeredCount() { return this.questions.filter(q => q.chosen_text !== null).length; },
        next() { if (this.index < this.questions.length - 1) this.index++; },
        prev() { if (this.index > 0) this.index--; },

        finish() {
            this.score = this.questions.filter(q => q.chosen_text === q.correct_text).length;
            this.phase = 'result';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        // ── result ──
        get percentage() {
            return this.questions.length ? Math.round((this.score / this.questions.length) * 100) : 0;
        },
        get scoreBand() {
            const p = this.percentage;
            return p >= 80 ? 'good' : (p >= 50 ? 'mid' : 'low');
        },

        submitResult() {
            this.submitting = true;
            const payload = {
                vessel_name: this.vesselName.trim(),
                rank: this.rankKey,
                score: this.score,
                total: this.questions.length,
                answers: this.questions.map(q => ({
                    question_id: q.question_id,
                    question_type: q.question_type,
                    chosen_text: q.chosen_text || '',
                    correct_text: q.correct_text,
                    is_correct: q.chosen_text === q.correct_text,
                })),
            };
            this.$refs.payload.value = JSON.stringify(payload);
            this.$refs.submitForm.submit();
        },

        reset() {
            this.phase = 'setup';
            this.questions = [];
            this.index = 0;
            this.score = 0;
            this.rankKey = '';
            this.setupError = '';
            this.submitting = false;
        },

        // Fisher-Yates
        shuffle(arr) {
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
        },
    };
}
</script>
@endpush
