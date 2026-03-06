@extends('layouts.app')

@section('title', 'Admin Analytics Dashboard')
@section('subtitle', 'Track adoption, voice profile growth, and translation throughput in one executive command center.')

@section('content')
    <div class="grid">
        <div class="card">
            <span class="pill">Global KPI</span>
            <h3>Total translations</h3>
            <p style="font-size:2rem; margin:0;">{{ $summary['total_translations'] ?? 0 }}</p>
        </div>
        <div class="card">
            <span class="pill">Voice AI KPI</span>
            <h3>Total voice profiles</h3>
            <p style="font-size:2rem; margin:0;">{{ $summary['total_voice_profiles'] ?? 0 }}</p>
        </div>
    </div>

    <div class="card">
        <h3>Recent translations</h3>
        <table>
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Target</th>
                    <th>Text</th>
                    <th>Translated</th>
                    <th>Provider</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($summary['recent_translations'] ?? []) as $row)
                    <tr>
                        <td>{{ $row['source_language'] }}</td>
                        <td>{{ $row['target_language'] }}</td>
                        <td>{{ $row['source_text'] }}</td>
                        <td>{{ $row['translated_text'] }}</td>
                        <td>{{ $row['provider'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">No recent translation activity.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
