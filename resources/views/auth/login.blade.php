<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Koneva</title>
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="/" class="logo-link">
                        <img src="/logo.png" alt="Koneva Logo" class="nav-logo">
                        <h2 class="logo-text"><span>Koneva</span></h2>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="/#home">Beranda</a></li>
                    <li><a href="/#services">Layanan</a></li>
                    <li><a href="/#contact">Kontak</a></li>
                </ul>
                <div class="nav-controls">
                    <a href="{{ route('login') }}" class="nav-auth-link">Login</a>
                    <a href="{{ route('register') }}" class="nav-auth-link">Register</a>
                    <button id="darkModeToggle" class="dark-mode-btn" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <main class="auth-shell">
        <div class="container">
            <section class="auth-card" style="max-width: 560px; margin: 0 auto;">
                <div class="section-header center">
                    <span class="section-tag">Portal Koneva</span>
                    <h1>Masuk ke <span>Portal</span></h1>
                    <p>Akses dashboard klien dan tim internal dengan akun Anda.</p>
                </div>

                @if (session('status'))
                    <div class="auth-status show">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="contact-form auth-form" autocomplete="on">
                    @csrf
                    <div class="form-group">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus autocomplete="username">
                    </div>
                    <div class="form-group">
                        <input id="password" type="password" name="password" placeholder="Password" required autocomplete="current-password">
                    </div>
                    <div class="form-group" style="flex-direction: row; align-items: center; gap: 0.6rem;">
                        <input id="remember_me" type="checkbox" name="remember" style="width: auto;">
                        <label for="remember_me" style="color: var(--text-light);">Remember me</label>
                    </div>
                    @if (config('services.altcha.enabled'))
                        <div class="form-group" style="margin-top: 0.4rem;">
                            <altcha-widget challenge="{{ route('altcha.challenge') }}" name="altcha"></altcha-widget>
                            @error('altcha')
                                <div class="auth-status show error" style="margin-top:0.5rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    <div style="display: flex; gap: 0.8rem; align-items: center; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary">Masuk</button>
                        <a href="{{ route('google.redirect') }}" class="nav-auth-link" style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <i class="fab fa-google"></i>
                            <span>Masuk dengan Google</span>
                        </a>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="nav-auth-link">Lupa Password?</a>
                        @endif
                    </div>
                </form>

                <p style="margin-top: 1rem; color: var(--text-light);">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="nav-auth-link" style="margin-left: 0.4rem;">Buat Akun</a>
                </p>
            </section>
        </div>
    </main>

    <footer class="footer compact-footer">
        <div class="container">
            <div class="footer-bottom">
                <p>© 2026 Koneva. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    @if (config('services.altcha.enabled'))
        <script async defer src="https://cdn.jsdelivr.net/gh/altcha-org/altcha/dist/altcha.min.js" type="module"></script>
    @endif
    <script src="/script.js"></script>
</body>
</html>
