<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Requests | Koneva</title>
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
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                    <li><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                    <li><a href="{{ route('admin.requests.index') }}" class="active">Requests</a></li>
                    <li><a href="{{ route('admin.deliverables.index') }}">Deliverables</a></li>
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
                    <span class="section-tag">Request Triage</span>
                    <h1>Client <span>Request Queue</span></h1>
                    <p>Review, triage status, and open per-request history details.</p>
                </div>

                <div class="portal-card" style="margin-bottom: 1rem;">
                    <form method="GET" action="{{ route('admin.requests.index') }}" class="contact-form auth-form" style="display:flex; gap:0.8rem; align-items:center;">
                        <div class="form-group" style="margin:0; flex:1;">
                            <select name="status" onchange="this.form.submit()">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected($statusFilter === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('admin.requests.index') }}" class="nav-auth-link">Reset</a>
                    </form>
                </div>

                <div class="portal-card" style="overflow-x:auto;">
                    @if ($requests->isEmpty())
                        <p>No request found for this filter.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:0.6rem;">Date</th>
                                    <th style="text-align:left; padding:0.6rem;">Client</th>
                                    <th style="text-align:left; padding:0.6rem;">Project</th>
                                    <th style="text-align:left; padding:0.6rem;">Title</th>
                                    <th style="text-align:left; padding:0.6rem;">Status</th>
                                    <th style="text-align:left; padding:0.6rem;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $item)
                                    <tr>
                                        <td style="padding:0.6rem;">{{ $item->created_at->format('Y-m-d') }}</td>
                                        <td style="padding:0.6rem;">{{ $item->client->company_name }}</td>
                                        <td style="padding:0.6rem;">{{ $item->project?->title ?? '-' }}</td>
                                        <td style="padding:0.6rem;">{{ $item->title }}</td>
                                        <td style="padding:0.6rem;">{{ $item->status }}</td>
                                        <td style="padding:0.6rem;"><a href="{{ route('admin.requests.show', $item) }}" class="nav-auth-link">Open</a></td>
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
