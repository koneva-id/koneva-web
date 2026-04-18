@verbatim
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Koneva</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="/" class="logo-link">
                        <img src="logo.png" alt="Koneva Logo" class="nav-logo">
                        <h2 class="logo-text"><span>Koneva</span></h2>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="/#home">Beranda</a></li>
                    <li><a href="/#services">Layanan</a></li>
                    <li><a href="/#contact">Kontak</a></li>
                </ul>
                <div class="nav-controls">
                    <a href="/login" class="nav-auth-link" data-auth="guest">Login</a>
                    <a href="/portal" class="nav-auth-link" data-auth="user" hidden>Portal</a>
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
            <section class="auth-card">
                <div class="section-header center">
                    <span class="section-tag">Portal Koneva</span>
                    <h1>Login atau <span>Daftar</span></h1>
                    <p>Akses portal klien dan tim internal dengan akun Anda.</p>
                </div>

                <div id="authStatus" class="auth-status" aria-live="polite"></div>

                <div class="auth-grid">
                    <form id="loginForm" class="contact-form auth-form" autocomplete="on">
                        <h3>Login</h3>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </form>

                    <form id="registerForm" class="contact-form auth-form" autocomplete="on">
                        <h3>Register</h3>
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" minlength="8" required>
                        </div>
                        <div class="form-group">
                            <select name="role" required>
                                <option value="client">Client</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Buat Akun</button>
                    </form>
                </div>
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

    <script src="auth.js"></script>
    <script src="script.js"></script>
</body>
</html>
@endverbatim
