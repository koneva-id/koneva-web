<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal | Koneva</title>
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
                    <li><a href="#overview">Overview</a></li>
                    <li><a href="#requests">Requests</a></li>
                    <li><a href="#account">Account</a></li>
                </ul>
                <div class="nav-controls">
                    <a href="/portal" class="nav-auth-link">Portal</a>
                    <button id="darkModeToggle" class="dark-mode-btn" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" id="logoutBtn" class="portal-logout-btn">Logout</button>
                    </form>
                </div>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <main class="portal-shell">
        <section id="overview" class="portal-section">
            <div class="container">
                <div class="section-header">
                    <span class="section-tag">Portal Overview</span>
                    <h1>Halo, <span>{{ auth()->user()->name }}</span></h1>
                    <p>Email terdaftar: <strong>{{ auth()->user()->email }}</strong>. Ini pondasi portal v1 untuk client dan admin.</p>
                </div>
                <div class="portal-grid">
                    <article class="portal-card">
                        <h3>Status Paket</h3>
                        <p>Belum ada paket aktif di akun demo ini.</p>
                    </article>
                    <article class="portal-card">
                        <h3>Progress Bulanan</h3>
                        <p>Dashboard KPI akan ditambahkan setelah backend Laravel aktif.</p>
                    </article>
                    <article class="portal-card">
                        <h3>Tugas Terbaru</h3>
                        <p>Belum ada task baru.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="requests" class="portal-section muted">
            <div class="container">
                <div class="section-header">
                    <h2>Requests & Submission</h2>
                    <p>V1 akan mendukung upload brief, revisi, dan lampiran aset dari klien.</p>
                </div>
                <div class="portal-card">
                    <p>Fitur request form backend belum diaktifkan. Tahap berikutnya: integrasi database + file storage.</p>
                </div>
            </div>
        </section>

        <section id="account" class="portal-section">
            <div class="container">
                <div class="section-header">
                    <h2>Account</h2>
                    <p>Detail akun dasar dari session Laravel.</p>
                </div>
                <div class="portal-card">
                    <p>Nama: <strong>{{ auth()->user()->name }}</strong></p>
                    <p>Email: <strong>{{ auth()->user()->email }}</strong></p>
                    <p>Role: <strong>{{ auth()->user()->role ?? 'client' }}</strong></p>
                    <p>Status login saat ini dikelola penuh oleh backend Laravel Breeze.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer compact-footer">
        <div class="container">
            <div class="footer-bottom">
                <p>© 2026 Koneva. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="/script.js"></script>
</body>
</html>
