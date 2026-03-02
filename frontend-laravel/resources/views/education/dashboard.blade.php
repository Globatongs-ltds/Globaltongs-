@extends('layouts.app')

@section('title', 'Learning Management Dashboard')
@section('subtitle', 'Manage schools, student enrollments, and language entitlements from one education command center.')

@section('content')
<div class="grid">
    <div class="card"><span class="pill">Schools</span><h3>{{ $stats['schools'] }}</h3></div>
    <div class="card"><span class="pill">Students</span><h3>{{ $stats['students'] }}</h3></div>
    <div class="card"><span class="pill">Active Classes</span><h3>{{ $stats['active_classes'] }}</h3></div>
    <div class="card"><span class="pill">Languages Enabled</span><h3>{{ $stats['languages_enabled'] }}</h3></div>
</div>
<div class="card">
    <h3>Onboarding Actions</h3>
    <p><a class="btn" href="{{ route('schools.register') }}">Register a School</a></p>
    <p><a class="btn" href="{{ route('students.register') }}">Register a Student</a></p>
</div>
@endsection
