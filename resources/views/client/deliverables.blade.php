<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Deliverables | Koneva</title>
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
                    <li><a href="{{ route('client.requests.index') }}">Requests</a></li>
                    <li><a href="{{ route('client.deliverables.index') }}" class="active">Deliverables</a></li>
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
                    <span class="section-tag">Deliverables</span>
                    <h1>File <span>Deliverables</span></h1>
                    <p>Daftar file hasil kerja untuk akun Anda.</p>
                </div>

                <div class="portal-card">
                    @if ($deliverables->isEmpty())
                        <p>Belum ada deliverable yang tersedia.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">Judul</th>
                                    <th style="text-align:left; padding: 0.6rem;">Project</th>
                                    <th style="text-align:left; padding: 0.6rem;">Versi</th>
                                    <th style="text-align:left; padding: 0.6rem;">Status</th>
                                    <th style="text-align:left; padding: 0.6rem;">Akses File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliverables as $item)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $item->title }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->project?->title ?? '-' }}</td>
                                        <td style="padding: 0.6rem;">v{{ $item->version }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->status }}</td>
                                        <td style="padding: 0.6rem;">
                                            @if ($item->file_url)
                                                <a href="{{ $item->file_url }}" target="_blank" rel="noopener noreferrer" class="nav-auth-link">Open</a>
                                            @else
                                                -
                                            @endif
                                        </td>
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
