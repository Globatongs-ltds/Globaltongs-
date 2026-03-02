<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EducationController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'schools' => 24,
            'students' => 1820,
            'active_classes' => 132,
            'languages_enabled' => 30,
        ];

        return view('education.dashboard', ['stats' => $stats]);
    }

    public function schoolRegister(): View
    {
        return view('education.school-register');
    }

    public function schoolRegisterSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:180'],
            'admin_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'country' => ['required', 'string', 'max:80'],
        ]);

        $code = 'SCH-' . strtoupper(substr(md5($validated['school_name'] . $validated['email']), 0, 8));

        return back()->with('success', 'School registration submitted successfully.')->with('school_code', $code);
    }

    public function studentRegister(): View
    {
        return view('education.student-register');
    }

    public function studentRegisterSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email'],
            'school_code' => ['required', 'string', 'min:6'],
            'plan' => ['required', 'in:basic,pro'],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['string', 'size:2'],
        ]);

        $selectedLanguages = $validated['languages'];
        if ($validated['plan'] === 'basic' && count($selectedLanguages) > 1) {
            return back()->with('error', 'Basic student plan allows only 1 language choice. Upgrade to Pro for more languages.')->withInput();
        }

        $message = $validated['plan'] === 'basic'
            ? 'Student registered under school with 1-language basic plan.'
            : 'Student registered under school with Pro plan and multi-language access.';

        return back()->with('success', $message)->with('student_plan', $validated['plan']);
    }
}
