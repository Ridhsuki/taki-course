<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#171124">
    <meta name="description" content="Create your Taki Course account and start learning today.">
    <title>Create Account - Taki Course</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell auth-shell--register">
        <section class="auth-visual" aria-label="Taki Course introduction">
            <a href="{{ route('front.index') }}" class="auth-logo" aria-label="Taki Course home">
                <img src="{{ asset('assets/logo/logo-white-custom.png') }}" alt="Taki Course">
            </a>

            <div class="visual-copy">
                <span class="visual-eyebrow">Start today</span>
                <h1>Learn new skills.<br>Build your future.</h1>
                <p>Create your account and get access to practical courses designed to move your career forward.</p>
            </div>

            <div class="visual-bottom visual-points">
                <div class="visual-point">
                    <span class="visual-point-icon">✓</span>
                    <span>Practical, project-based lessons</span>
                </div>
                <div class="visual-point">
                    <span class="visual-point-icon">✓</span>
                    <span>Learn anytime from any device</span>
                </div>
                <div class="visual-point">
                    <span class="visual-point-icon">✓</span>
                    <span>Guidance from experienced mentors</span>
                </div>
            </div>
        </section>

        <section class="auth-form-panel">
            <a href="{{ route('front.index') }}" class="back-home">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 18l-6-6 6-6"/></svg>
                Back to home
            </a>

            <div class="auth-form-wrap auth-form-wrap--register">
                <header class="form-heading">
                    <span class="form-kicker">Create account</span>
                    <h2>Join Taki Course</h2>
                    <p>Complete your profile and start learning today.</p>
                </header>

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="auth-form auth-form--register">
                    @csrf

                    <div class="form-field">
                        <label for="name">Full name</label>
                        <div class="form-control">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your full name">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="field-error" />
                    </div>

                    <div class="form-field">
                        <label for="email">Email address</label>
                        <div class="form-control">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="field-error" />
                    </div>

                    <div class="form-field">
                        <label for="occupation">Occupation</label>
                        <div class="form-control">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 13.255A23.9 23.9 0 0112 15c-3.18 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input id="occupation" type="text" name="occupation" value="{{ old('occupation') }}" required autocomplete="organization-title" placeholder="Student, Designer, Developer">
                        </div>
                        <x-input-error :messages="$errors->get('occupation')" class="field-error" />
                    </div>

                    <div class="form-field">
                        <label for="avatar">Profile photo</label>
                        <label for="avatar" class="file-control">
                            <span class="file-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <span class="file-copy">
                                <strong id="file-name">No photo selected</strong>
                                <small>JPG, PNG or WEBP · max 2MB</small>
                            </span>
                            <span class="file-action">Browse</span>
                            <input id="avatar" class="file-input" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
                        </label>
                        <x-input-error :messages="$errors->get('avatar')" class="field-error" />
                    </div>

                    <div class="form-field">
                        <label for="password">Password</label>
                        <div class="form-control">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 characters">
                            <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.46 12C3.73 7.94 7.52 5 12 5c4.48 0 8.27 2.94 9.54 7-1.27 4.06-5.06 7-9.54 7-4.48 0-8.27-2.94-9.54-7z"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="field-error" />
                    </div>

                    <div class="form-field">
                        <label for="password_confirmation">Confirm password</label>
                        <div class="form-control">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password">
                            <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.46 12C3.73 7.94 7.52 5 12 5c4.48 0 8.27 2.94 9.54 7-1.27 4.06-5.06 7-9.54 7-4.48 0-8.27-2.94-9.54-7z"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="field-error" />
                    </div>

                    <button type="submit" class="submit-button form-span-2">
                        <span>Create Account</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

                <p class="form-switch">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </p>
            </div>
        </section>
    </main>

    <script>
        const avatarInput = document.getElementById('avatar');
        const fileName = document.getElementById('file-name');

        avatarInput.addEventListener('change', () => {
            fileName.textContent = avatarInput.files[0]?.name || 'No photo selected';
        });

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
