@extends('layouts.app')

@section('title', 'Referral Package')
@section('subtitle', 'Share World Voice Teach and earn recurring rewards for every active referred customer.')

@section('content')
<div class="grid">
    <div class="card">
        <h3>Affiliate Starter</h3>
        <p class="muted">20% recurring commission for 6 months.</p>
        <ul>
            <li>Personal referral dashboard</li>
            <li>Marketing assets</li>
            <li>Monthly payouts</li>
        </ul>
    </div>
    <div class="card">
        <h3>Partner Pro</h3>
        <p class="muted">30% recurring commission + co-marketing.</p>
        <ul>
            <li>Priority partner manager</li>
            <li>Sales enablement kit</li>
            <li>Joint webinar opportunities</li>
        </ul>
    </div>
</div>
<div class="card">
    <a class="btn" href="{{ route('register') }}">Apply to Referral Program</a>
</div>
@endsection
