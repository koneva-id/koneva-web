<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard | Koneva</title>
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="sidebar-layout">
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
                    <li><a href="{{ url('/') }}"><i class="fas fa-home" style="margin-right:0.3rem;"></i>Home</a></li>
                    <li><a href="{{ route('client.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('client.requests.index') }}">Requests</a></li>
                    <li><a href="{{ route('client.deliverables.index') }}">Deliverables</a></li>
                    <li><a href="{{ route('client.insights.index') }}">Insights</a></li>
                    <li><a href="{{ route('profile.settings') }}">Profile</a></li>
                </ul>
                <div class="nav-controls">
                    <button class="hamburger" aria-label="Toggle menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <button id="darkModeToggle" class="dark-mode-btn" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="portal-logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="portal-shell">
        <section class="portal-section">
            <div class="container">
                @if (session('login_success'))
                    <div class="auth-status show" style="text-align:center; font-size:1.05rem; padding: 0.75rem 1rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; margin-bottom: 1.5rem;">
                        <i class="fas fa-check-circle" style="margin-right:0.5rem;"></i>{{ session('login_success') }}
                    </div>
                    <div class="auth-status show" style="text-align:center; font-size:0.98rem; padding: 0.65rem 1rem; background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.25); border-radius: 12px; margin-bottom: 1.5rem; color: var(--text-light);">
                        <i class="fas fa-info-circle" style="margin-right:0.5rem; color: #6366f1;"></i>
                        Silakan pergi ke halaman <a href="{{ url('/') }}" style="color:#6366f1; font-weight:600;">Home</a> untuk memesan paket layanan.
                    </div>
                @endif
                <div class="section-header">
                    <span class="section-tag">Client Portal</span>
                    <h1>Halo, <span>{{ auth()->user()->name }}</span></h1>
                    <p>{{ $client?->company_name ?? 'Client profile is not configured yet.' }}</p>
                </div>

                <div class="portal-grid">
                    <article class="portal-card">
                        <h3>Total Projects</h3>
                        <p>{{ $projectCount }}</p>
                    </article>
                    <article class="portal-card">
                        <h3>Total Requests</h3>
                        <p>{{ $requestCount }}</p>
                    </article>
                    <article class="portal-card">
                        <h3>Total Deliverables</h3>
                        <p>{{ $deliverableCount }}</p>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <script src="/script.js"></script>
</body>
</html>
