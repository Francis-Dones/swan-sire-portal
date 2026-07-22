<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Inspection Images Export</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    h1 { font-size: 16px; color: #0C3D73; margin-bottom: 4px; }
    .meta { color: #6b7280; font-size: 9px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0C3D73; color: white; padding: 7px 8px; text-align: left; font-size: 9px; }
    td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) td { background: #f8fafc; }
</style>
</head>
<body>
<h1>🚢 Ship Inspection Portal — Inspection Images</h1>
<p class="meta">Generated: {{ now()->format('F d, Y H:i') }} | Total: {{ count($images) }}</p>
<table>
    <thead>
        <tr><th>#</th><th>Image Name</th><th>Vessel ID</th><th>Inspection ID</th><th>Type</th><th>MIME</th><th>Created</th></tr>
    </thead>
    <tbody>
        @foreach($images as $i => $img)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $img['image_name'] ?? '—' }}</td>
            <td>{{ $img['vessel_id'] ?? '—' }}</td>
            <td>{{ $img['inspection_id'] ?? '—' }}</td>
            <td>{{ $img['inspection_type'] ?? '—' }}</td>
            <td>{{ $img['image_mime_type'] ?? '—' }}</td>
            <td>{{ isset($img['created_at']) ? date('M d, Y', strtotime($img['created_at'])) : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
