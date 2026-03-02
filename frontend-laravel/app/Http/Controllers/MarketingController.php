<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function register(): View
    {
        return view('marketing.register');
    }

    public function registerSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'email_verification_code' => ['required', 'digits:6'],
            'phone_verification_code' => ['required', 'digits:6'],
            'accept_terms' => ['accepted'],
        ]);

        return back()->with('success', 'Registration submitted successfully. Account pending final verification.')->with('registered_user', $validated['email']);
    }

    public function trial(): View
    {
        return view('marketing.trial');
    }

    public function trialSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'company' => ['nullable', 'string', 'max:120'],
            'team_size' => ['required', 'integer', 'min:1'],
            'use_case' => ['required', 'string', 'max:500'],
        ]);

        return back()->with('success', 'Trial request submitted. We will activate your 14-day trial shortly.');
    }

    public function pricing(): View
    {
        return view('marketing.pricing');
    }

    public function referral(): View
    {
        return view('marketing.referral');
    }

    public function contact(): View
    {
        return view('marketing.contact');
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return back()->with('success', 'Thank you for contacting us. Our team will respond within 24 hours.');
    }

    public function team(): View
    {
        return view('marketing.team');
    }
}
