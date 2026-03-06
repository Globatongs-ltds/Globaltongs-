<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World Voice Teach</title>
    <style>
        :root {
            --bg: #090c1b;
            --card: rgba(19, 27, 54, 0.8);
            --line: rgba(151, 173, 255, 0.2);
            --text: #ebefff;
            --muted: #a5b2df;
            --accent: #6f8bff;
            --accent2: #2de2e6;
            --success: #6cffb3;
            --danger: #ff7ea8;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: var(--text);
            font-family: Inter, Segoe UI, Roboto, Arial, sans-serif;
            background:
                radial-gradient(circle at 10% 10%, #1e2f6e 0%, transparent 35%),
                radial-gradient(circle at 90% 20%, #1c8fa3 0%, transparent 30%),
                var(--bg);
            min-height: 100vh;
        }
        .container { max-width: 1150px; margin: 0 auto; padding: 1.25rem; }
        .nav {
            position: sticky; top: 0; z-index: 5;
            backdrop-filter: blur(12px);
            background: rgba(9, 12, 27, 0.75);
            border-bottom: 1px solid var(--line);
        }
        .nav .inner { display: flex; flex-wrap: wrap; gap: .7rem; align-items: center; justify-content: space-between; }
        .brand { font-weight: 700; letter-spacing: .3px; }
        .menu { display: flex; flex-wrap: wrap; gap: .5rem; }
        .menu a {
            color: var(--text); text-decoration: none; font-size: .93rem;
            border: 1px solid var(--line); padding: .45rem .75rem; border-radius: 999px;
            background: rgba(151,173,255,0.06);
        }
        .hero { padding: 1.8rem 0 1.2rem; }
        .hero h1 { margin: 0; font-size: clamp(1.6rem, 3vw, 2.4rem); }
        .hero p { color: var(--muted); max-width: 780px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
        .card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: linear-gradient(160deg, rgba(111,139,255,0.1), rgba(45,226,230,0.04));
            box-shadow: 0 12px 32px rgba(0,0,0,.28);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .muted { color: var(--muted); }
        .success { color: var(--success); }
        .error { color: var(--danger); }
        label { font-size: .9rem; color: var(--muted); }
        input, textarea, select {
            width: 100%; margin-top: .4rem; margin-bottom: .8rem;
            border-radius: 10px; border: 1px solid var(--line);
            background: rgba(9,12,27,0.65); color: var(--text);
            padding: .68rem .8rem;
        }
        .btn {
            display: inline-block; border: none; cursor: pointer;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            color: #0a1024; font-weight: 700; padding: .66rem 1rem; border-radius: 10px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid var(--line); padding: .6rem; text-align: left; }
        th { color: var(--muted); }
        .pill { display: inline-block; padding: .2rem .55rem; border-radius: 999px; border: 1px solid var(--line); color: var(--muted); font-size: .8rem; }
    </style>
</head>
<body>
    <div class="nav">
        <div class="container inner">
            <div class="brand">🌍 World Voice Teach</div>
            <div class="menu">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('pricing') }}">Pricing</a>
                <a href="{{ route('referral') }}">Referral</a>
                <a href="{{ route('team') }}">Team</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('trial') }}">Free Trial</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="{{ route('lms.dashboard') }}">LMS</a>
                <a href="{{ route('schools.register') }}">Schools</a>
                <a href="{{ route('students.register') }}">Students</a>
                <a href="{{ route('payments.index') }}">Payments</a>
                <a href="{{ route('admin.dashboard') }}">Admin</a>
                <a href="{{ route('user.dashboard') }}">User</a>
            </div>
        </div>
    </div>

    <div class="container hero">
        <h1>@yield('title', 'AI Realtime Translation Platform')</h1>
        <p>@yield('subtitle', 'Ultra-low-latency multilingual speech translation with modern dashboards, billing, and growth workflows.')</p>

        @if(session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif
        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        @yield('content')
    </div>
</body>
</html>
