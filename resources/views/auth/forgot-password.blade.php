<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - KONEVA</title>
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
                <li><a href="{{ route('login') }}" class="nav-auth-link">Masuk</a></li>
                <li><a href="{{ route('register') }}" class="nav-auth-link">Daftar</a></li>
            </ul>
        </nav>
    </header>

    <section class="contact" style="padding-top: 140px; min-height: calc(100vh - 120px);">
        <div class="container" style="max-width: 620px;">
            <div class="section-header" style="margin-bottom: 1.4rem;">
                <h2>Reset Password</h2>
                <p>Masukkan email akun Anda. Kami akan mengirim tautan untuk membuat password baru.</p>
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
                <form method="POST" action="{{ route('password.email') }}" class="contact-form auth-form">
                    @csrf
                    <div class="form-group">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email akun" autocomplete="username">
                    </div>
                    @if (config('services.altcha.enabled'))
                        <div class="form-group" style="margin-top: 0.4rem;">
                            <altcha-widget challenge="{{ route('altcha.challenge') }}" name="altcha"></altcha-widget>
                            @error('altcha')
                                <div class="auth-status show error" style="margin-top:0.5rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    <div style="display:flex; gap:0.8rem; align-items:center; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
                        <a href="{{ route('login') }}" class="nav-auth-link">Kembali ke Login</a>
                    </div>
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
