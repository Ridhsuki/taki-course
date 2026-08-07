<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#171124">
    <meta name="description" content="Sign in to continue learning with Taki Course.">
    <title>Sign In - Taki Course</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-visual" aria-label="Taki Course introduction">
            <a href="{{ route('front.index') }}" class="auth-logo" aria-label="Taki Course home">
                <img src="{{ asset('assets/logo/logo-white-custom.png') }}" alt="Taki Course">
            </a>

            <div class="visual-copy">
                <span class="visual-eyebrow">Welcome back</span>
                <h1>Keep growing,<br>one lesson at a time.</h1>
                <p>Continue your learning journey and turn every new skill into a better opportunity.</p>
            </div>

            <div class="visual-bottom">
                <div class="visual-stats">
                    <div class="visual-stat">
                        <strong>3K+</strong>
                        <span>Active students</span>
                    </div>
                    <div class="visual-stat">
                        <strong>50+</strong>
                        <span>Practical courses</span>
                    </div>
                    <div class="visual-stat">
                        <strong>4.9</strong>
                        <span>Student rating</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="auth-form-panel">
            <a href="{{ route('front.index') }}" class="back-home">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 18l-6-6 6-6"/></svg>
                Back to home
            </a>

            <div class="auth-form-wrap">
                <header class="form-heading">
                    <span class="form-kicker">Sign in</span>
                    <h2>Welcome back</h2>
                    <p>Enter your account details to continue learning.</p>
                </header>

                @if (session('status'))
                    <div class="status-message">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-field">
                        <label for="email">Email address</label>
                        <div class="form-control">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="field-error" />
                    </div>

                    <div class="form-field">
                        <label for="password">Password</label>
                        <div class="form-control">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                            <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.46 12C3.73 7.94 7.52 5 12 5c4.48 0 8.27 2.94 9.54 7-1.27 4.06-5.06 7-9.54 7-4.48 0-8.27-2.94-9.54-7z"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="field-error" />
                    </div>

                    <div class="form-options">
                        <label for="remember_me" class="remember-option">
                            <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-link">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="submit-button">
                        <span>Sign In</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

                <p class="form-switch">
                    Don't have an account?
                    <a href="{{ route('register') }}">Create account</a>
                </p>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const showPassword = input.type === 'password';
                input.type = showPassword ? 'text' : 'password';
                button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>
