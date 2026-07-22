<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Users Export</title>
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
<h1>🚢 Ship Inspection Portal — Users</h1>
<p class="meta">Generated: {{ now()->format('F d, Y H:i') }} | Total: {{ count($users) }}</p>
<table>
    <thead><tr><th>#</th><th>Username</th><th>Email</th><th>Address</th><th>Age</th><th>Token Type</th><th>Created</th></tr></thead>
    <tbody>
        @foreach($users as $i => $u)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $u['username'] ?? '—' }}</td>
            <td>{{ $u['email'] ?? '—' }}</td>
            <td>{{ $u['address'] ?? '—' }}</td>
            <td>{{ $u['age'] ?? '—' }}</td>
            <td>{{ $u['token_type'] ?? '—' }}</td>
            <td>{{ isset($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
