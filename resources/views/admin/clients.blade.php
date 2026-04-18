<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Clients | Koneva</title>
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
                    <li><a href="{{ route('admin.clients.index') }}" class="active">Clients</a></li>
                    <li><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                    <li><a href="{{ route('admin.requests.index') }}">Requests</a></li>
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
                    <span class="section-tag">Client Management</span>
                    <h1>Kelola <span>Clients</span></h1>
                    <p>Buat profile client berdasarkan akun user role client.</p>
                </div>

                @if (session('status'))
                    <div class="auth-status show">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <div class="portal-card" style="margin-bottom: 1rem;">
                    <form method="POST" action="{{ route('admin.clients.store') }}" class="contact-form auth-form">
                        @csrf
                        <div class="form-group">
                            <select name="user_id" required>
                                <option value="">Pilih User Client</option>
                                @foreach ($clientUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="company_name" placeholder="Company Name" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone" placeholder="Phone (optional)">
                        </div>
                        <div class="form-group">
                            <input type="text" name="industry" placeholder="Industry (optional)">
                        </div>
                        <div class="form-group">
                            <select name="status" required>
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Client Profile</button>
                    </form>
                </div>

                <div class="portal-card">
                    <h3>Client Profiles</h3>
                    @if ($clients->isEmpty())
                        <p>No client profiles yet.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">User</th>
                                    <th style="text-align:left; padding: 0.6rem;">Email</th>
                                    <th style="text-align:left; padding: 0.6rem;">Company</th>
                                    <th style="text-align:left; padding: 0.6rem;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $item)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $item->user->name }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->user->email }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->company_name }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->status }}</td>
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
