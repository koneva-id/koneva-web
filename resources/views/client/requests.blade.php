<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Requests | Koneva</title>
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
                    <li><a href="{{ route('client.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('client.requests.index') }}" class="active">Requests</a></li>
                    <li><a href="{{ route('client.deliverables.index') }}">Deliverables</a></li>
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
                    <span class="section-tag">Requests</span>
                    <h1>Buat <span>Request</span> Baru</h1>
                    <p>Submit brief atau revisi untuk tim admin.</p>
                </div>

                @if (session('status'))
                    <div class="auth-status show">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <div class="portal-card" style="margin-bottom: 1rem;">
                    <form method="POST" action="{{ route('client.requests.store') }}" class="contact-form auth-form">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="title" placeholder="Judul Request" value="{{ old('title') }}" required>
                        </div>
                        <div class="form-group">
                            <select name="project_id">
                                <option value="">Pilih Project (opsional)</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="message" rows="5" placeholder="Deskripsikan kebutuhan Anda" required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Request</button>
                    </form>
                </div>

                <div class="portal-card">
                    <h3>Riwayat Requests</h3>
                    @if ($clientRequests->isEmpty())
                        <p>Belum ada request.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">Judul</th>
                                    <th style="text-align:left; padding: 0.6rem;">Project</th>
                                    <th style="text-align:left; padding: 0.6rem;">Status</th>
                                    <th style="text-align:left; padding: 0.6rem;">Komentar Admin</th>
                                    <th style="text-align:left; padding: 0.6rem;">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientRequests as $item)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $item->title }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->project?->title ?? '-' }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->status }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->histories->first()?->client_note ?? '-' }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->created_at->format('Y-m-d') }}</td>
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
