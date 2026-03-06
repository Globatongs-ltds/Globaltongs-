@extends('layouts.app')

@section('title', 'Realtime Voice & Text Translation')
@section('subtitle', 'Experience high-fidelity translation across 30 major languages with instant output for teams, support, and classrooms.')

@section('content')
    <div class="grid">
        <div class="card">
            <span class="pill">Translator Console</span>
            <form method="POST" action="{{ route('translate') }}" style="margin-top:.8rem;">
                @csrf
                <label>Text</label>
                <textarea name="text" rows="4">{{ old('text') }}</textarea>

                <label>Source language</label>
                <select name="source_language">
                    @foreach($languages as $language)
                        <option value="{{ $language['code'] }}" @selected(old('source_language', $defaultSource) === $language['code'])>
                            {{ $language['name'] }} ({{ $language['code'] }})
                        </option>
                    @endforeach
                </select>

                <label>Target language</label>
                <select name="target_language">
                    @foreach($languages as $language)
                        <option value="{{ $language['code'] }}" @selected(old('target_language', $defaultTarget) === $language['code'])>
                            {{ $language['name'] }} ({{ $language['code'] }})
                        </option>
                    @endforeach
                </select>

                <button class="btn" type="submit">Translate Now</button>
            </form>
        </div>

        <div class="card">
            <span class="pill">Output</span>
            @if(session('translated_text'))
                <h3>Translated Text</h3>
                <p class="success">{{ session('translated_text') }}</p>
            @else
                <p class="muted">No translation yet. Submit text to see live translated output.</p>
            @endif

            <hr style="border-color: rgba(151,173,255,.2);">
            <h3>Why this experience is high-tech</h3>
            <ul>
                <li>Realtime backend architecture with websocket streaming support.</li>
                <li>Integrated pricing, referrals, and trial onboarding flow.</li>
                <li>End-to-end payment gateway integration (Flutterwave + PayPal).</li>
            </ul>
        </div>
    </div>
@endsection
