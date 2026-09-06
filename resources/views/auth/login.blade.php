<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/hms.css') }}" rel="stylesheet">
</head>
<body class="hms-login-body">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="hms-login-card">
            <div class="hms-login-brand mb-4">
                <span class="hms-brand-mark">H</span>
                <div><strong>Hospital Management System</strong><small>Staff Portal · Academic Prototype</small></div>
            </div>

            <div class="mb-4">
                <h1 class="h3 mb-2">Welcome back</h1>
                <p class="text-secondary mb-0">Sign in with your hospital staff account to continue.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input id="email" name="email" type="email" class="form-control form-control-lg" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" class="form-control form-control-lg" required>
                </div>
                <div class="form-check mb-4">
                    <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1">
                    <label for="remember" class="form-check-label">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Sign in</button>
            </form>

            <p class="text-center text-secondary small mt-4 mb-0">Final-year Computer Science project prototype</p>
        </div>
    </main>
</body>
</html>
