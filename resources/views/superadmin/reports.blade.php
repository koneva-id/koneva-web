<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Reporting | Koneva</title>
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
                    <li><a href="{{ route('superadmin.audit-logs.index') }}">Audit Logs</a></li>
                    <li><a href="{{ route('superadmin.billing.index') }}">Billing</a></li>
                    <li><a href="{{ route('superadmin.reports.index') }}" class="active">Reporting</a></li>
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
                    <span class="section-tag">Reporting</span>
                    <h1>Business <span>Reports</span></h1>
                    <p>Ringkasan performa operasional dan finansial lintas modul.</p>
                </div>

                <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
                    <a href="{{ route('superadmin.reports.csv') }}" class="nav-auth-link">Download CSV</a>
                </div>

                <div class="portal-grid" style="margin-bottom: 1rem;">
                    <article class="portal-card"><h3>Total Users</h3><p>{{ $metrics['totalUsers'] }}</p></article>
                    <article class="portal-card"><h3>Active Users</h3><p>{{ $metrics['activeUsers'] }}</p></article>
                    <article class="portal-card"><h3>Total Clients</h3><p>{{ $metrics['totalClients'] }}</p></article>
                    <article class="portal-card"><h3>Total Projects</h3><p>{{ $metrics['totalProjects'] }}</p></article>
                    <article class="portal-card"><h3>Total Requests</h3><p>{{ $metrics['totalRequests'] }}</p></article>
                    <article class="portal-card"><h3>Total Deliverables</h3><p>{{ $metrics['totalDeliverables'] }}</p></article>
                    <article class="portal-card"><h3>Total Invoices</h3><p>{{ $metrics['totalInvoices'] }}</p></article>
                    <article class="portal-card"><h3>Paid Revenue</h3><p>IDR {{ number_format((float) $metrics['paidRevenue'], 2) }}</p></article>
                    <article class="portal-card"><h3>Outstanding Revenue</h3><p>IDR {{ number_format((float) $metrics['outstandingRevenue'], 2) }}</p></article>
                </div>

                <div class="portal-card" style="overflow-x: auto;">
                    <h3>Monthly Billing Trend</h3>
                    @if ($monthlyRevenue->isEmpty())
                        <p>No billing data available yet.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">Month</th>
                                    <th style="text-align:left; padding: 0.6rem;">Invoiced</th>
                                    <th style="text-align:left; padding: 0.6rem;">Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyRevenue as $row)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $row->month }}</td>
                                        <td style="padding: 0.6rem;">IDR {{ number_format((float) $row->invoiced_total, 2) }}</td>
                                        <td style="padding: 0.6rem;">IDR {{ number_format((float) $row->paid_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script src="/script.js"></script>
</body>
</html>
