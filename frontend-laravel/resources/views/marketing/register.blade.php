@extends('layouts.app')

@section('title', 'Create Your Account')
@section('subtitle', 'Sign up with built-in email and phone verification to secure your multilingual workspace.')

@section('content')
<div class="card" style="max-width:820px;">
    <form method="POST" action="{{ route('register.submit') }}">
        @csrf
        <div class="grid">
            <div>
                <label>Full Name</label>
                <input name="full_name" value="{{ old('full_name') }}" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label>Phone</label>
                <input name="phone" value="{{ old('phone') }}" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <label>Email Verification Code (6 digits)</label>
                <input name="email_verification_code" pattern="\d{6}" value="{{ old('email_verification_code') }}" required>
            </div>
            <div>
                <label>Phone Verification Code (6 digits)</label>
                <input name="phone_verification_code" pattern="\d{6}" value="{{ old('phone_verification_code') }}" required>
            </div>
        </div>

        <label style="display:flex; gap:.5rem; align-items:center;">
            <input type="checkbox" name="accept_terms" value="1" style="width:auto; margin:0;"> I accept terms and privacy policy.
        </label>

        <button class="btn" type="submit">Create Secure Account</button>
    </form>

    @if(session('registered_user'))
        <p class="success" style="margin-top:1rem;">Registered: {{ session('registered_user') }}</p>
    @endif
</div>
@endsection
