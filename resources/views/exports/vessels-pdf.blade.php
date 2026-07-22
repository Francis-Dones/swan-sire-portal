<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vessels Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1f2937;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #1f2937;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Vessels Report</h1>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Vessel Name</th>
                <th>Total Exams</th>
                <th>Last Inspection</th>
                <th>Persons In Charge</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vessels as $i => $vessel)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $vessel['vessel_name'] }}</td>
                <td>{{ $vessel['total_exams'] }}</td>
                <td>{{ $vessel['last_inspection'] ? date('M d, Y', strtotime($vessel['last_inspection'])) : 'No inspection' }}</td>
                <td>{{ $vessel['persons'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Vessels: {{ count($vessels) }}</p>
    </div>
</body>
</html>