<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard | Koneva</title>
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
                    <li><a href="{{ route('superadmin.dashboard') }}" class="active">Dashboard</a></li>
                    <li><a href="{{ route('superadmin.admins.index') }}">Admin Management</a></li>
                    <li><a href="{{ route('superadmin.audit-logs.index') }}">Audit Logs</a></li>
                    <li><a href="{{ route('superadmin.billing.index') }}">Billing</a></li>
                    <li><a href="{{ route('superadmin.reports.index') }}">Reporting</a></li>
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
                <div class="section-header">
                    <span class="section-tag">Superadmin Portal</span>
                    <h1>Halo, <span>{{ auth()->user()->name }}</span></h1>
                    <p>Kontrol admin-management, audit logs, billing, dan reporting.</p>
                </div>

                <div class="portal-grid">
                    <article class="portal-card">
                        <h3>Privileged Accounts</h3>
                        <p>{{ $adminCount }}</p>
                    </article>
                    <article class="portal-card">
                        <h3>Inactive Users</h3>
                        <p>{{ $inactiveCount }}</p>
                    </article>
                    <article class="portal-card">
                        <h3>Total Audit Entries</h3>
                        <p>{{ $auditCount }}</p>
                    </article>
                    <article class="portal-card">
                        <h3>Total Invoices</h3>
                        <p>{{ $invoiceCount }}</p>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <script src="/script.js"></script>
</body>
</html>
