<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function home(): View
    {
        $backend = config('worldvoice.backend_url');

        $languages = Http::get("{$backend}/languages")->json() ?? [];

        return view('home', [
            'languages' => $languages,
            'defaultSource' => config('worldvoice.default_source_language'),
            'defaultTarget' => config('worldvoice.default_target_language'),
        ]);
    }

    public function admin(): View
    {
        $backend = config('worldvoice.backend_url');

        $summary = Http::get("{$backend}/dashboard/admin/summary", ['limit' => 10])->json() ?? [];

        return view('admin-dashboard', ['summary' => $summary]);
    }

    public function user(): View
    {
        $backend = config('worldvoice.backend_url');

        $summary = Http::get("{$backend}/dashboard/user/summary", ['limit' => 10])->json() ?? [];

        return view('user-dashboard', ['summary' => $summary]);
    }

    public function translate(Request $request): RedirectResponse
    {
        $request->validate([
            'text' => ['required', 'string'],
            'source_language' => ['required', 'string'],
            'target_language' => ['required', 'string'],
        ]);

        $backend = config('worldvoice.backend_url');

        $response = Http::post("{$backend}/translate", $request->only([
            'text',
            'source_language',
            'target_language',
        ]));

        if (! $response->successful()) {
            return back()->with('error', 'Translation failed. Please try again.')->withInput();
        }

        return back()->with('translated_text', $response->json('translated_text'));
    }
}
