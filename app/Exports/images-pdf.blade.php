<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inspection Images Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Inspection Images Report</h1>
    <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    <p>Total: {{ count($images) }} images</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image Name</th>
                <th>Vessel ID</th>
                <th>Inspection ID</th>
                <th>Inspection Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($images as $img)
            <tr>
                <td>{{ $img['id'] ?? $img['image_id'] ?? 'N/A' }}</td>
                <td>{{ $img['image_name'] ?? $img['name'] ?? 'N/A' }}</td>
                <td>{{ $img['vessel_id'] ?? $img['vesselId'] ?? 'N/A' }}</td>
                <td>{{ $img['inspection_id'] ?? $img['inspectionId'] ?? 'N/A' }}</td>
                <td>{{ $img['inspection_type'] ?? $img['inspectionType'] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>