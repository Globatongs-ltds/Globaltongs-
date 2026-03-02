<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('payments');
    }

    public function initialize(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'gateway' => ['required', 'in:flutterwave,paypal,mock'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'customer_email' => ['required', 'email'],
            'reference' => ['required', 'string', 'min:3'],
            'callback_url' => ['nullable', 'url'],
        ]);

        $backend = config('worldvoice.backend_url');
        $response = Http::post("{$backend}/payments/initialize", $payload);

        if (! $response->successful()) {
            return back()->with('error', 'Payment initialization failed.')->withInput();
        }

        $paymentLink = $response->json('payment_link');

        if (! $paymentLink) {
            return back()->with('error', 'Gateway returned no payment link.')->withInput();
        }

        return redirect()->away($paymentLink);
    }
}
