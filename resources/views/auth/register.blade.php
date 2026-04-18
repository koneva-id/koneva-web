<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Koneva</title>
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
                    <h1>Buat Akun <span>Baru</span></h1>
                    <p>Daftarkan akun untuk mengakses portal Koneva.</p>
                </div>

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="contact-form auth-form" autocomplete="on">
                    @csrf
                    <div class="form-group">
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap" required autofocus autocomplete="name">
                    </div>
                    <div class="form-group">
                        <input id="organization" type="text" name="organization" value="{{ old('organization') }}" placeholder="Organisasi/Perusahaan (opsional)" autocomplete="organization">
                    </div>
                    <div class="form-group">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <input id="password" type="password" name="password" placeholder="Password" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Konfirmasi Password" required autocomplete="new-password">
                    </div>
                    @if (config('services.altcha.enabled'))
                        <div class="form-group" style="margin-top: 0.4rem;">
                            <altcha-widget challenge="{{ route('altcha.challenge') }}" name="altcha"></altcha-widget>
                            @error('altcha')
                                <div class="auth-status show error" style="margin-top:0.5rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    <div style="display:flex; gap:0.6rem; align-items:center; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">Buat Akun</button>
                        <a href="{{ route('google.redirect') }}" class="nav-auth-link" style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <i class="fab fa-google"></i>
                            <span>Daftar dengan Google</span>
                        </a>
                    </div>
                </form>

                <p style="margin-top: 1rem; color: var(--text-light);">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="nav-auth-link" style="margin-left: 0.4rem;">Masuk</a>
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
