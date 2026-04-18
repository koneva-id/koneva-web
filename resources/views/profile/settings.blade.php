<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | Koneva</title>
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
                    <li><a href="{{ route('portal') }}">Portal</a></li>
                    <li><a href="{{ route('profile.settings') }}" class="active">Profile Settings</a></li>
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
                    <span class="section-tag">Account</span>
                    <h1>Profile <span>Settings</span></h1>
                    <p>Update profile information, password, and account state.</p>
                </div>

                @if (session('status') === 'profile-updated')
                    <div class="auth-status show">Profile updated.</div>
                @endif

                @if (session('status') === 'password-updated')
                    <div class="auth-status show">Password updated.</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <div class="portal-card" style="margin-bottom: 1rem;">
                    <h3>Update Profile</h3>
                    <form method="POST" action="{{ route('profile.update') }}" class="contact-form auth-form">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </form>
                </div>

                <div class="portal-card" style="margin-bottom: 1rem;">
                    <h3>Change Password</h3>
                    <form method="POST" action="{{ route('password.update') }}" class="contact-form auth-form">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <input type="password" name="current_password" placeholder="Current Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="New Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password_confirmation" placeholder="Confirm New Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>

                <div class="portal-card">
                    <h3>Delete Account</h3>
                    <form method="POST" action="{{ route('profile.destroy') }}" class="contact-form auth-form">
                        @csrf
                        @method('DELETE')
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Confirm your current password" required>
                        </div>
                        <button type="submit" class="nav-auth-link" onclick="return confirm('Delete your account permanently?')">Delete Account</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="/script.js"></script>
</body>
</html>
