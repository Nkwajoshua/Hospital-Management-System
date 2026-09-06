<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hospital Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/hms.css') }}" rel="stylesheet">
</head>
<body class="hms-body">
@auth
    @php
        $role = auth()->user()->role;
        $roleLabel = ucwords(str_replace('_', ' ', $role));
    @endphp

    <div class="hms-shell">
        <aside class="hms-sidebar d-none d-lg-flex flex-column">
            <a href="{{ route('dashboard') }}" class="hms-brand text-decoration-none">
                <span class="hms-brand-mark">H</span>
                <span>
                    <strong>HMS</strong>
                    <small>Clinical Prototype</small>
                </span>
            </a>

            <div class="hms-sidebar-label">Workspace</div>
            <nav class="hms-nav flex-grow-1">
                <a href="{{ route('dashboard') }}" class="hms-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('patients.index') }}" class="hms-nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">Patients</a>

                @if (in_array($role, ['admin', 'receptionist', 'doctor'], true))
                    <a href="{{ route('appointments.index') }}" class="hms-nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">Appointments</a>
                @endif

                @if (in_array($role, ['admin', 'doctor'], true))
                    <a href="{{ route('consultations.index') }}" class="hms-nav-link {{ request()->routeIs('consultations.*') ? 'active' : '' }}">Medical Records</a>
                @endif

                @if (in_array($role, ['admin', 'doctor', 'lab_staff'], true))
                    <a href="{{ route('lab-tests.index') }}" class="hms-nav-link {{ request()->routeIs('lab-tests.*') ? 'active' : '' }}">Laboratory</a>
                @endif

                @if (in_array($role, ['admin', 'doctor', 'pharmacist'], true))
                    <a href="{{ route('prescriptions.index') }}" class="hms-nav-link {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">Prescriptions</a>
                @endif

                @if (in_array($role, ['admin', 'pharmacist'], true))
                    <a href="{{ route('medicines.index') }}" class="hms-nav-link {{ request()->routeIs('medicines.*') ? 'active' : '' }}">Medicines</a>
                @endif

                @if (in_array($role, ['admin', 'receptionist'], true))
                    <a href="{{ route('billing.index') }}" class="hms-nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}">Billing</a>
                @endif

                @if ($role === 'admin')
                    <div class="hms-sidebar-label mt-4">Administration</div>
                    <a href="{{ route('staff.index') }}" class="hms-nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">Staff Accounts</a>
                @endif
            </nav>

            <div class="hms-sidebar-user">
                <div class="small fw-semibold text-white">{{ auth()->user()->name }}</div>
                <div class="small opacity-75">{{ $roleLabel }}</div>
            </div>
        </aside>

        <div class="hms-main">
            <header class="hms-topbar">
                <div>
                    <div class="fw-semibold">Hospital Management System</div>
                    <div class="small text-secondary d-none d-sm-block">Undergraduate final-year working prototype</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge rounded-pill hms-role-badge">{{ $roleLabel }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Logout</button>
                    </form>
                </div>
            </header>

            <nav class="hms-mobile-nav d-lg-none">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.*') ? 'active' : '' }}">Patients</a>
                @if (in_array($role, ['admin', 'receptionist', 'doctor'], true))<a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">Appointments</a>@endif
                @if (in_array($role, ['admin', 'doctor'], true))<a href="{{ route('consultations.index') }}" class="{{ request()->routeIs('consultations.*') ? 'active' : '' }}">Records</a>@endif
                @if (in_array($role, ['admin', 'doctor', 'lab_staff'], true))<a href="{{ route('lab-tests.index') }}" class="{{ request()->routeIs('lab-tests.*') ? 'active' : '' }}">Lab</a>@endif
                @if (in_array($role, ['admin', 'doctor', 'pharmacist'], true))<a href="{{ route('prescriptions.index') }}" class="{{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">Prescriptions</a>@endif
                @if (in_array($role, ['admin', 'pharmacist'], true))<a href="{{ route('medicines.index') }}" class="{{ request()->routeIs('medicines.*') ? 'active' : '' }}">Medicines</a>@endif
                @if (in_array($role, ['admin', 'receptionist'], true))<a href="{{ route('billing.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">Billing</a>@endif
                @if ($role === 'admin')<a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">Staff</a>@endif
            </nav>

            <main class="hms-content">
                @if (session('success'))<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>@endif
                @if (session('error'))<div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>@endif
                @yield('content')
            </main>

            <footer class="hms-footer">Hospital Management System · Academic MVP</footer>
        </div>
    </div>
@else
    @yield('content')
@endauth
</body>
</html>
