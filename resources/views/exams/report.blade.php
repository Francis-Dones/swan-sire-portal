@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inspection Report: {{ $exam['vessel_name'] ?? 'N/A' }}</h3>
                    <div class="card-tools">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                        <a href="{{ route('exams.show', $exam['id']) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Report Header -->
                    <div class="text-center mb-4">
                        <h2>Ship Inspection Report</h2>
                        <h4>{{ $exam['vessel_name'] ?? 'N/A' }}</h4>
                        <p>Exam ID: {{ $exam['exam_id'] ?? 'N/A' }} | Date: {{ date('Y-m-d H:i') }}</p>
                    </div>

                    <!-- Executive Summary -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">Executive Summary</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <h1>{{ $stats['compliance_percentage'] }}%</h1>
                                            <p>Overall Compliance</p>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-success" style="width: {{ $stats['compliance_percentage'] }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h1 class="text-success">{{ $stats['yes'] }}</h1>
                                            <p>Compliant Items</p>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h1 class="text-danger">{{ $stats['no'] }}</h1>
                                            <p>Non-Conformities</p>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <h1>{{ $stats['grade'] }}</h1>
                                            <p>Overall Grade</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Compliance Breakdown</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="complianceChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Section Performance</h5>
                                </div>
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        @foreach($sectionStats as $section => $sectionStat)
                                            <tr>
                                                <td>{{ $section }}</td>
                                                <td width="100">
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar {{ $sectionStat['compliance_percentage'] >= 80 ? 'bg-success' : ($sectionStat['compliance_percentage'] >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                             style="width: {{ $sectionStat['compliance_percentage'] }}%">
                                                            {{ $sectionStat['compliance_percentage'] }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Non-Conformities Section -->
                    @if(count($noResponses) > 0)
                        <div class="card mb-4 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h4 class="mb-0">⚠️ Non-Conformities ({{ count($noResponses) }})</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Question</th>
                                            <th>Responsible</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($noResponses as $nc)
                                            <tr>
                                                <td><strong>{{ $nc['code'] }}</strong></td>
                                                <td>{{ $nc['question'] }}</td>
                                                <td>{{ $nc['responsible'] }}</td>
                                                <td>{{ $nc['remarks'] ?: 'No remarks provided' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Detailed Questionnaire Results -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Detailed Questionnaire Results</h4>
                        </div>
                        <div class="card-body">
                            @foreach($groupedResponses as $section => $questions)
                                <div class="card mb-3">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">{{ $section }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th width="10%">Code</th>
                                                        <th width="50%">Question</th>
                                                        <th width="10%">Response</th>
                                                        <th width="30%">Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($questions as $code => $data)
                                                        <tr class="{{ $data['response'] == 'no' ? 'table-danger' : ($data['response'] == 'yes' ? 'table-success' : '') }}">
                                                            <td><strong>{{ $code }}</strong></td>
                                                            <td>{{ $data['question']['text'] }}</td>
                                                            <td>
                                                                <span class="badge {{ $data['response'] == 'yes' ? 'badge-success' : ($data['response'] == 'no' ? 'badge-danger' : 'badge-secondary') }}">
                                                                    {{ strtoupper($data['response']) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $data['remarks'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Report Footer -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><strong>Inspected by:</strong> {{ $questionnaire['completed_by'] ?? 'N/A' }}</p>
                            <p><strong>Inspection Date:</strong> {{ isset($questionnaire['completed_at']) ? date('Y-m-d H:i', strtotime($questionnaire['completed_at'])) : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p><strong>Report Generated:</strong> {{ date('Y-m-d H:i:s') }}</p>
                            <p><strong>Signature:</strong> _________________</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('complianceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Yes (Compliant)', 'No (Non-Conformity)', 'N/A'],
                datasets: [{
                    data: [{{ $stats['yes'] }}, {{ $stats['no'] }}, {{ $stats['na'] }}],
                    backgroundColor: ['#28a745', '#dc3545', '#6c757d'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Overall Compliance Distribution'
                    }
                }
            }
        });
    });
</script>

<style>
    @media print {
        .card-tools, .btn, .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            break-inside: avoid;
        }
        .table {
            font-size: 10pt;
        }
    }
    .badge-success {
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
    }
    .badge-danger {
        background-color: #dc3545;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
    }
    .badge-secondary {
        background-color: #6c757d;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
    }
    .table-danger {
        background-color: #f8d7da;
    }
    .table-success {
        background-color: #d4edda;
    }
</style>
@endsection