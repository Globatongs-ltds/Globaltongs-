@extends('layouts.app')

@section('title', 'Register for Free Trial')
@section('subtitle', 'Launch a 14-day trial with premium realtime translation capabilities and onboarding support.')

@section('content')
<div class="card" style="max-width:860px;">
    <form method="POST" action="{{ route('trial.submit') }}">
        @csrf
        <div class="grid">
            <div>
                <label>Name</label>
                <input name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label>Company</label>
                <input name="company" value="{{ old('company') }}">
            </div>
            <div>
                <label>Team Size</label>
                <input type="number" min="1" name="team_size" value="{{ old('team_size', 5) }}" required>
            </div>
        </div>

        <label>Primary Use Case</label>
        <textarea name="use_case" rows="4" required>{{ old('use_case') }}</textarea>

        <button class="btn" type="submit">Start My Trial</button>
    </form>
</div>
@endsection
