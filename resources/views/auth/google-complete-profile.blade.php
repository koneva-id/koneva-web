<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil - KONEVA</title>
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo"><a href="/" style="text-decoration:none; color:inherit;">KONEVA</a></div>
            <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
            <ul class="nav-menu" id="nav-menu">
                <li><a href="/#home" class="nav-link">Home</a></li>
                <li><a href="/#features" class="nav-link">Fitur</a></li>
                <li><a href="/#pricing" class="nav-link">Harga</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-auth-link" style="background:none;border:none;cursor:pointer;">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <section class="contact" style="padding-top: 140px; min-height: calc(100vh - 120px);">
        <div class="container" style="max-width: 620px;">
            <div class="section-header" style="margin-bottom: 1.4rem;">
                <h2>Lengkapi Profil Google</h2>
                <p>Satu langkah lagi. Isi nama lengkap dan organisasi/perusahaan (opsional) sebelum masuk dashboard.</p>
            </div>

            @if (session('status'))
                <div class="auth-status show success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="auth-status show error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="contact-form-container">
                <form method="POST" action="{{ route('google.complete-profile.store') }}" class="contact-form auth-form" autocomplete="on">
                    @csrf
                    <div class="form-group">
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama Lengkap" required autofocus autocomplete="name">
                    </div>
                    <div class="form-group">
                        <input id="organization" type="text" name="organization" value="{{ old('organization', $user->organization) }}" placeholder="Organisasi/Perusahaan (opsional)" autocomplete="organization">
                    </div>
                    @if (config('services.altcha.enabled'))
                        <div class="form-group" style="margin-top: 0.4rem;">
                            <altcha-widget challenge="{{ route('altcha.challenge') }}" name="altcha"></altcha-widget>
                            @error('altcha')
                                <div class="auth-status show error" style="margin-top:0.5rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    <button type="submit" class="btn btn-primary">Lanjut ke Dashboard</button>
                </form>
            </div>
        </div>
    </section>

    @if (config('services.altcha.enabled'))
        <script async defer src="https://cdn.jsdelivr.net/gh/altcha-org/altcha/dist/altcha.min.js" type="module"></script>
    @endif
    <script src="/script.js"></script>
</body>
</html>
