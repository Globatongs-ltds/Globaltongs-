<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World Voice Teach Frontend</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        nav a { margin-right: 1rem; }
        .card { border: 1px solid #ddd; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .error { color: #b00020; }
        .ok { color: #0a7a32; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
        <a href="{{ route('user.dashboard') }}">User Dashboard</a>
        <a href="{{ route('payments.index') }}">Payments</a>
    </nav>
    <hr>

    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    @yield('content')
</body>
</html>
