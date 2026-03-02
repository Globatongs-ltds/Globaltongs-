@extends('layouts.app')

@section('title', 'User Activity Dashboard')
@section('subtitle', 'Monitor your translation usage, recent output quality, and productivity gains across sessions.')

@section('content')
    <div class="card">
        <span class="pill">Usage</span>
        <h3>Your translation count (global in current API)</h3>
        <p style="font-size:2rem; margin:0;">{{ $summary['total_translations'] ?? 0 }}</p>
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
                @forelse(($summary['recent_translations'] ?? []) as $row)
                    <tr>
                        <td>{{ $row['source_language'] }}</td>
                        <td>{{ $row['target_language'] }}</td>
                        <td>{{ $row['source_text'] }}</td>
                        <td>{{ $row['translated_text'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">No recent records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
