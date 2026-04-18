<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Admin Management | Koneva</title>
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
                    <li><a href="{{ route('superadmin.admins.index') }}" class="active">Admin Management</a></li>
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
                    <span class="section-tag">Admin Management</span>
                    <h1>Kelola <span>Privileged Users</span></h1>
                    <p>Buat akun admin/superadmin dan kelola role serta status aktif user.</p>
                </div>

                @if (session('status'))
                    <div class="auth-status show">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <div class="portal-card" style="margin-bottom: 1rem;">
                    <form method="POST" action="{{ route('superadmin.admins.store') }}" class="contact-form auth-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <input type="text" name="name" placeholder="Full Name" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="password" name="password" placeholder="Temporary Password" required>
                            </div>
                            <div class="form-group">
                                <select name="role" required>
                                    <option value="admin">admin</option>
                                    <option value="superadmin">superadmin</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Privileged Account</button>
                    </form>
                </div>

                <div class="portal-card" style="margin-bottom: 1rem; overflow-x: auto;">
                    <h3>Admin & Superadmin Accounts</h3>
                    @if ($admins->isEmpty())
                        <p>Belum ada akun admin/superadmin.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">Name</th>
                                    <th style="text-align:left; padding: 0.6rem;">Email</th>
                                    <th style="text-align:left; padding: 0.6rem;">Role</th>
                                    <th style="text-align:left; padding: 0.6rem;">Status</th>
                                    <th style="text-align:left; padding: 0.6rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $item)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $item->name }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->email }}</td>
                                        <td style="padding: 0.6rem;">
                                            <form method="POST" action="{{ route('superadmin.admins.update-role', $item) }}" style="display:flex; gap:0.4rem;">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" style="padding:0.35rem; border-radius:8px; border:1px solid #cbd5e1;">
                                                    <option value="admin" @selected($item->role === 'admin')>admin</option>
                                                    <option value="superadmin" @selected($item->role === 'superadmin')>superadmin</option>
                                                    <option value="client" @selected($item->role === 'client')>client</option>
                                                </select>
                                                <button type="submit" class="nav-auth-link">Save</button>
                                            </form>
                                        </td>
                                        <td style="padding: 0.6rem;">{{ $item->is_active ? 'active' : 'inactive' }}</td>
                                        <td style="padding: 0.6rem;">
                                            <form method="POST" action="{{ route('superadmin.admins.toggle-status', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="nav-auth-link">{{ $item->is_active ? 'Deactivate' : 'Activate' }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="portal-card" style="overflow-x: auto;">
                    <h3>Client Accounts (Role/Status Management)</h3>
                    @if ($clients->isEmpty())
                        <p>No client users found.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">Name</th>
                                    <th style="text-align:left; padding: 0.6rem;">Email</th>
                                    <th style="text-align:left; padding: 0.6rem;">Status</th>
                                    <th style="text-align:left; padding: 0.6rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $item)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $item->name }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->email }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->is_active ? 'active' : 'inactive' }}</td>
                                        <td style="padding: 0.6rem; display:flex; gap:0.4rem;">
                                            <form method="POST" action="{{ route('superadmin.admins.update-role', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="admin">
                                                <button type="submit" class="nav-auth-link">Promote to Admin</button>
                                            </form>
                                            <form method="POST" action="{{ route('superadmin.admins.toggle-status', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="nav-auth-link">{{ $item->is_active ? 'Deactivate' : 'Activate' }}</button>
                                            </form>
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
