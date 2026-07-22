<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Exam Records Export</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
    h1 { font-size: 16px; color: #0C3D73; margin-bottom: 4px; }
    .meta { color: #6b7280; font-size: 9px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0C3D73; color: white; padding: 7px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; }
    td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) td { background: #f8fafc; }
    .badge { background: #dbeafe; color: #1d4ed8; padding: 2px 6px; border-radius: 9999px; font-size: 8px; }
</style>
</head>
<body>
<h1>🚢 Ship Inspection Portal — Exam Records</h1>
<p class="meta">Generated: {{ now()->format('F d, Y H:i') }} | Total Records: {{ count($exams) }}</p>
<table>
    <thead>
        <tr><th>#</th><th>Exam ID</th><th>Vessel</th><th>Person In Charge</th><th>Submitted By</th><th>Email</th><th>Date</th></tr>
    </thead>
    <tbody>
        @foreach($exams as $i => $exam)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><span class="badge">{{ $exam['exam_id'] ?? '—' }}</span></td>
            <td>{{ $exam['vessel_name'] ?? '—' }}</td>
            <td>{{ $exam['person_in_charge'] ?? '—' }}</td>
            <td>{{ $exam['submitted_by'] ?? '—' }}</td>
            <td>{{ $exam['email'] ?? '—' }}</td>
            <td>{{ isset($exam['submitted_date']) ? date('M d, Y', strtotime($exam['submitted_date'])) : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
