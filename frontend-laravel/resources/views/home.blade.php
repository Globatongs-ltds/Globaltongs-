@extends('layouts.app')

@section('content')
    <h1>Realtime Translation</h1>

    <div class="card">
        <form method="POST" action="{{ route('translate') }}">
            @csrf
            <label>Text</label><br>
            <textarea name="text" rows="4" style="width:100%;">{{ old('text') }}</textarea><br><br>

            <label>Source language</label><br>
            <select name="source_language">
                @foreach($languages as $language)
                    <option value="{{ $language['code'] }}" @selected(old('source_language', $defaultSource) === $language['code'])>
                        {{ $language['name'] }} ({{ $language['code'] }})
                    </option>
                @endforeach
            </select><br><br>

            <label>Target language</label><br>
            <select name="target_language">
                @foreach($languages as $language)
                    <option value="{{ $language['code'] }}" @selected(old('target_language', $defaultTarget) === $language['code'])>
                        {{ $language['name'] }} ({{ $language['code'] }})
                    </option>
                @endforeach
            </select><br><br>

            <button type="submit">Translate</button>
        </form>
    </div>

    @if(session('translated_text'))
        <div class="card">
            <h3>Translated text</h3>
            <p class="ok">{{ session('translated_text') }}</p>
        </div>
    @endif
@endsection
