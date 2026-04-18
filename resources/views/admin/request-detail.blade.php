<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Request Detail | Koneva</title>
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
                    <span class="section-tag">Request Detail</span>
                    <h1>{{ $clientRequest->title }}</h1>
                    <p>{{ $clientRequest->client->company_name }} - {{ $clientRequest->project?->title ?? 'No project' }}</p>
                </div>

                @if (session('status'))
                    <div class="auth-status show">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <div class="portal-card" style="margin-bottom:1rem;">
                    <h3>Original Request</h3>
                    <p><strong>Submitted by:</strong> {{ $clientRequest->submitter->name }} ({{ $clientRequest->submitter->email }})</p>
                    <p><strong>Current status:</strong> {{ $clientRequest->status }}</p>
                    <p style="white-space: pre-wrap;">{{ $clientRequest->message }}</p>
                </div>

                <div class="portal-card" style="margin-bottom:1rem;">
                    <h3>Triage Update</h3>
                    <form method="POST" action="{{ route('admin.requests.update', $clientRequest) }}" class="contact-form auth-form">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <select name="status" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected($clientRequest->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="internal_note" rows="4" placeholder="Internal note (admin only)"></textarea>
                        </div>
                        <div class="form-group">
                            <textarea name="client_note" rows="3" placeholder="Client-facing note"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Triage Update</button>
                    </form>
                </div>

                <div class="portal-card" style="overflow-x:auto;">
                    <h3>Request History</h3>
                    @if ($clientRequest->histories->isEmpty())
                        <p>No history entries yet.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:0.6rem;">Time</th>
                                    <th style="text-align:left; padding:0.6rem;">Actor</th>
                                    <th style="text-align:left; padding:0.6rem;">Status</th>
                                    <th style="text-align:left; padding:0.6rem;">Internal Note</th>
                                    <th style="text-align:left; padding:0.6rem;">Client Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientRequest->histories as $entry)
                                    <tr>
                                        <td style="padding:0.6rem;">{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                        <td style="padding:0.6rem;">{{ $entry->actor?->email ?? 'system' }}</td>
                                        <td style="padding:0.6rem;">{{ $entry->old_status ?? '-' }} -> {{ $entry->new_status ?? '-' }}</td>
                                        <td style="padding:0.6rem;">{{ $entry->internal_note ?? '-' }}</td>
                                        <td style="padding:0.6rem;">{{ $entry->client_note ?? '-' }}</td>
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
