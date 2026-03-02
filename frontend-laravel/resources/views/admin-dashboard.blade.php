@extends('layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>

    <div class="card">
        <p><strong>Total translations:</strong> {{ $summary['total_translations'] ?? 0 }}</p>
        <p><strong>Total voice profiles:</strong> {{ $summary['total_voice_profiles'] ?? 0 }}</p>
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
                @foreach(($summary['recent_translations'] ?? []) as $row)
                    <tr>
                        <td>{{ $row['source_language'] }}</td>
                        <td>{{ $row['target_language'] }}</td>
                        <td>{{ $row['source_text'] }}</td>
                        <td>{{ $row['translated_text'] }}</td>
                        <td>{{ $row['provider'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
