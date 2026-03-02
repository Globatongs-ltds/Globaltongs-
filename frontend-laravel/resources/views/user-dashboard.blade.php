@extends('layouts.app')

@section('content')
    <h1>User Dashboard</h1>

    <div class="card">
        <p><strong>Your translation count (global in current API):</strong> {{ $summary['total_translations'] ?? 0 }}</p>
    </div>

    <div class="card">
        <h3>Recent translations</h3>
        <table>
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Target</th>
                    <th>Original</th>
                    <th>Translated</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($summary['recent_translations'] ?? []) as $row)
                    <tr>
                        <td>{{ $row['source_language'] }}</td>
                        <td>{{ $row['target_language'] }}</td>
                        <td>{{ $row['source_text'] }}</td>
                        <td>{{ $row['translated_text'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
