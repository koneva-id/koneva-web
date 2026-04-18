<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil - KONEVA</title>
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
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-auth-link" style="background:none;border:none;cursor:pointer;">Logout</button>
                    </form>
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
            <section class="auth-card" style="max-width: 620px; margin: 0 auto;">
                <div class="section-header center" style="margin-bottom: 1.4rem;">
                <h2>Lengkapi Profil Google</h2>
                <p>Satu langkah lagi. Isi nama lengkap dan organisasi/perusahaan (opsional) sebelum masuk dashboard.</p>
                </div>

            @if (session('status'))
                <div class="auth-status show success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="auth-status show error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

                <form method="POST" action="{{ route('google.complete-profile.store') }}" class="contact-form auth-form" autocomplete="on">
                    @csrf
                    <div class="form-group">
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama Lengkap" required autofocus autocomplete="name">
                    </div>
                    <div class="form-group">
                        <input id="organization" type="text" name="organization" value="{{ old('organization', $user->organization) }}" placeholder="Organisasi/Perusahaan (opsional)" autocomplete="organization">
                    </div>
                    @if (config('services.altcha.enabled'))
                        <div class="form-group" style="margin-top: 0.4rem;">
                            <altcha-widget challenge="{{ route('altcha.challenge') }}" name="altcha"></altcha-widget>
                            @error('altcha')
                                <div class="auth-status show error" style="margin-top:0.5rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    <button type="submit" class="btn btn-primary">Lanjut ke Dashboard</button>
                </form>
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
