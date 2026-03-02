@extends('layouts.app')

@section('title', 'Secure Checkout & Billing')
@section('subtitle', 'Activate subscriptions and enterprise plans using Flutterwave or PayPal in a secure payment flow.')

@section('content')
    <div class="card" style="max-width:760px;">
        <form method="POST" action="{{ route('payments.initialize') }}">
            @csrf
            <label>Gateway</label>
            <select name="gateway">
                <option value="flutterwave">Flutterwave</option>
                <option value="paypal">PayPal</option>
                <option value="mock">Mock</option>
            </select>

            <label>Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', '10.00') }}">

            <label>Currency (3 letters)</label>
            <input type="text" name="currency" value="{{ old('currency', 'USD') }}">

            <label>Customer Email</label>
            <input type="email" name="customer_email" value="{{ old('customer_email', 'user@example.com') }}">

            <label>Reference</label>
            <input type="text" name="reference" value="{{ old('reference', 'order-1001') }}">

            <label>Callback URL</label>
            <input type="url" name="callback_url" value="{{ old('callback_url', 'https://example.com/payment/callback') }}">

            <button class="btn" type="submit">Initialize Payment</button>
        </form>
    </div>
@endsection
