<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Distributed KV Admin</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 14px; }
        th { background: #f0f0f0; text-align: left; }
        .deleted { color: #999; text-decoration: line-through; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 11px; }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-deleted { background: #f8d7da; color: #721c24; }
        .clients { margin-bottom: 20px; }
        .clients span { display: inline-block; margin-right: 8px; padding: 2px 6px; background: #eee; border-radius: 3px; }
    </style>
</head>
<body>

<h1>Distributed KV - Admin</h1>

<p>
    <strong>Client locale:</strong> {{ $clientName }}<br>
    <strong>Ultimo sync:</strong> {{ $lastSync ?? 'mai' }}
</p>

<div class="clients">
    <strong>Client conosciuti:</strong>
    @forelse($clients as $name => $url)
        <span>{{ $name }} ({{ $url }})</span>
    @empty
        <span>Nessun client configurato</span>
    @endforelse
</div>

<h2>Chiavi</h2>
<table>
    <thead>
    <tr>
        <th>Key</th>
        <th>Valore</th>
        <th>Version</th>
        <th>Updated at</th>
        <th>Deleted at</th>
    </tr>
    </thead>
    <tbody>
    @forelse($entries as $entry)
        <tr>
            <td class="{{ $entry->isDeleted() ? 'deleted' : '' }}">
                {{ $entry->key }}
            </td>
            <td class="{{ $entry->isDeleted() ? 'deleted' : '' }}">
                {{ \Illuminate\Support\Str::limit($entry->value, 80) }}
            </td>
            <td>{{ $entry->version }}</td>
            <td>{{ $entry->updated_at }}</td>
            <td>
                @if($entry->deleted_at)
                    <span class="badge badge-deleted">deleted: {{ $entry->deleted_at }}</span>
                @else
                    <span class="badge badge-active">active</span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5">Nessuna chiave presente.</td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
