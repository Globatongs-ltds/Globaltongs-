@extends('layouts.app')

@section('title', 'Pricing Tables')
@section('subtitle', 'Flexible plans for creators, startups, and global enterprises with realtime voice translation needs.')

@section('content')
<div class="grid">
    <div class="card">
        <span class="pill">Starter</span>
        <h3>$19 / month</h3>
        <p class="muted">For solo educators and creators.</p>
        <ul>
            <li>Up to 10k translated minutes</li>
            <li>30 language access</li>
            <li>Basic dashboard analytics</li>
        </ul>
    </div>
    <div class="card" style="border-color: rgba(111,139,255,.65)">
        <span class="pill">Pro</span>
        <h3>$79 / month</h3>
        <p class="muted">For teams running multilingual operations.</p>
        <ul>
            <li>Up to 100k translated minutes</li>
            <li>Priority latency routing</li>
            <li>Advanced admin insights</li>
        </ul>
    </div>
    <div class="card">
        <span class="pill">Enterprise</span>
        <h3>Custom</h3>
        <p class="muted">For global organizations and call centers.</p>
        <ul>
            <li>Unlimited scale</li>
            <li>Dedicated account architect</li>
            <li>SLA + custom integrations</li>
        </ul>
    </div>
</div>
@endsection
