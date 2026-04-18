<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Audit Logs | Koneva</title>
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
                    <li><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('superadmin.admins.index') }}">Admin Management</a></li>
                    <li><a href="{{ route('superadmin.audit-logs.index') }}" class="active">Audit Logs</a></li>
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
                    <span class="section-tag">Audit Logs</span>
                    <h1>System <span>Activity</span></h1>
                    <p>Jejak aktivitas kritikal dari modul admin, superadmin, client, dan billing.</p>
                </div>

                <div class="portal-card" style="overflow-x: auto;">
                    @if ($logs->isEmpty())
                        <p>No audit logs available.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">Time</th>
                                    <th style="text-align:left; padding: 0.6rem;">Actor</th>
                                    <th style="text-align:left; padding: 0.6rem;">Action</th>
                                    <th style="text-align:left; padding: 0.6rem;">Target</th>
                                    <th style="text-align:left; padding: 0.6rem;">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td style="padding: 0.6rem;">{{ $log->actor?->email ?? 'system' }}</td>
                                        <td style="padding: 0.6rem;">{{ $log->action }}</td>
                                        <td style="padding: 0.6rem;">{{ ($log->target_type ?? '-') }}{{ $log->target_id ? '#'.$log->target_id : '' }}</td>
                                        <td style="padding: 0.6rem;">{{ $log->description ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div style="margin-top: 1rem;">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script src="/script.js"></script>
</body>
</html>
