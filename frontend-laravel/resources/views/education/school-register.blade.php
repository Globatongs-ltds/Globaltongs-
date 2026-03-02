@extends('layouts.app')

@section('title', 'School Registration')
@section('subtitle', 'Register institutions to onboard teachers and students into structured multilingual learning.')

@section('content')
<div class="card" style="max-width:860px;">
    <form method="POST" action="{{ route('schools.register.submit') }}">
        @csrf
        <div class="grid">
            <div><label>School Name</label><input name="school_name" value="{{ old('school_name') }}" required></div>
            <div><label>Admin Name</label><input name="admin_name" value="{{ old('admin_name') }}" required></div>
            <div><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
            <div><label>Phone</label><input name="phone" value="{{ old('phone') }}" required></div>
            <div><label>Country</label><input name="country" value="{{ old('country') }}" required></div>
        </div>
        <button class="btn" type="submit">Register School</button>
    </form>

    @if(session('school_code'))
        <p class="success" style="margin-top:1rem;">School Code Generated: <strong>{{ session('school_code') }}</strong></p>
    @endif
</div>
@endsection
