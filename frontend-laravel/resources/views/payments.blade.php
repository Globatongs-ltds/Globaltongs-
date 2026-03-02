@extends('layouts.app')

@section('content')
    <h1>Payment Checkout</h1>

    <div class="card">
        <form method="POST" action="{{ route('payments.initialize') }}">
            @csrf
            <label>Gateway</label><br>
            <select name="gateway">
                <option value="flutterwave">Flutterwave</option>
                <option value="paypal">PayPal</option>
                <option value="mock">Mock</option>
            </select><br><br>

            <label>Amount</label><br>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', '10.00') }}"><br><br>

            <label>Currency (3 letters)</label><br>
            <input type="text" name="currency" value="{{ old('currency', 'USD') }}"><br><br>

            <label>Customer Email</label><br>
            <input type="email" name="customer_email" value="{{ old('customer_email', 'user@example.com') }}"><br><br>

            <label>Reference</label><br>
            <input type="text" name="reference" value="{{ old('reference', 'order-1001') }}"><br><br>

            <label>Callback URL</label><br>
            <input type="url" name="callback_url" value="{{ old('callback_url', 'https://example.com/payment/callback') }}"><br><br>

            <button type="submit">Initialize Payment</button>
        </form>
    </div>
@endsection
