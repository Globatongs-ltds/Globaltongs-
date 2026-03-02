@extends('layouts.app')

@section('title', 'Contact Us')
@section('subtitle', 'Talk to product specialists, integration engineers, and success managers.')

@section('content')
<div class="grid">
    <div class="card">
        <h3>Global Support Channels</h3>
        <p class="muted">Email: support@worldvoiceteach.ai</p>
        <p class="muted">Sales: sales@worldvoiceteach.ai</p>
        <p class="muted">Phone: +1 (555) 408-2219</p>
    </div>
    <div class="card">
        <form method="POST" action="{{ route('contact.submit') }}">
            @csrf
            <label>Name</label>
            <input name="name" value="{{ old('name') }}" required>

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>Message</label>
            <textarea name="message" rows="5" required>{{ old('message') }}</textarea>

            <button class="btn" type="submit">Send Message</button>
        </form>
    </div>
</div>
@endsection
