@extends('layouts.app')

@section('title', 'Student Registration')
@section('subtitle', 'Students are registered under schools. Basic plan allows 1 language; upgrade to Pro for multiple languages.')

@section('content')
<div class="card" style="max-width:920px;">
    <form method="POST" action="{{ route('students.register.submit') }}">
        @csrf
        <div class="grid">
            <div><label>Full Name</label><input name="full_name" value="{{ old('full_name') }}" required></div>
            <div><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
            <div><label>School Code</label><input name="school_code" value="{{ old('school_code') }}" required></div>
            <div>
                <label>Plan</label>
                <select name="plan">
                    <option value="basic" @selected(old('plan')==='basic')>Basic (1 Language)</option>
                    <option value="pro" @selected(old('plan')==='pro')>Pro (Multi-language)</option>
                </select>
            </div>
        </div>

        <label>Language Choices</label>
        <div class="grid">
            @php($langs = ['en','es','fr','de','ar','pt','zh','hi'])
            @foreach($langs as $lang)
                <label style="display:flex; gap:.45rem; align-items:center;">
                    <input style="width:auto; margin:0;" type="checkbox" name="languages[]" value="{{ $lang }}" @checked(is_array(old('languages')) && in_array($lang, old('languages')))> {{ strtoupper($lang) }}
                </label>
            @endforeach
        </div>

        <button class="btn" type="submit">Register Student</button>
    </form>

    @if(session('student_plan'))
        <p class="success" style="margin-top:1rem;">Student Plan: {{ strtoupper(session('student_plan')) }}</p>
    @endif
</div>
@endsection
