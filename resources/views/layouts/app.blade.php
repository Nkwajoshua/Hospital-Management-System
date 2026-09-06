<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hospital Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">Hospital Management System</a>

            @auth
                <div class="d-flex align-items-center gap-3">
                    <a class="btn btn-link text-decoration-none text-secondary p-0" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="btn btn-link text-decoration-none text-secondary p-0" href="{{ route('patients.index') }}">Patients</a>
                    @if (in_array(auth()->user()->role, ['admin', 'receptionist', 'doctor'], true))
                        <a class="btn btn-link text-decoration-none text-secondary p-0" href="{{ route('appointments.index') }}">Appointments</a>
                    @endif
                    <div class="text-end d-none d-lg-block ms-2">
                        <div class="fw-medium">{{ auth()->user()->name }}</div>
                        <small class="text-secondary text-capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</small>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
