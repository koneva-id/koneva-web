<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalisasi Bisnis | Koneva</title>
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .personalize-wrap {
            max-width: 720px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }
        .step-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .step-icon {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.6rem; color: #fff;
        }
        .step-header h1 { font-size: 1.75rem; margin: 0 0 0.5rem; }
        .step-header p { color: var(--text-light, #6b7280); font-size: 0.95rem; margin: 0; }

        .form-section {
            background: var(--card-bg, #fff);
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .form-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #6366f1;
            margin: 0 0 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border, #e5e7eb);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
        .form-group { margin-bottom: 1.1rem; }
        .form-group:last-child { margin-bottom: 0; }
        .form-group label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text, #374151);
            margin-bottom: 0.4rem;
        }
        .form-group label .opt {
            font-weight: 400;
            color: var(--text-light, #6b7280);
            font-size: 0.8rem;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.6rem 0.9rem;
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 10px;
            font-size: 0.9rem;
            background: var(--card-bg, #fff);
            color: var(--text, #374151);
            box-sizing: border-box;
            transition: border-color 0.15s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-hint {
            font-size: 0.76rem;
            color: var(--text-light, #6b7280);
            margin: 0.3rem 0 0;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.5rem;
        }
        .check-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: border-color 0.15s, background 0.15s;
        }
        .check-item:has(input:checked) {
            border-color: #6366f1;
            background: rgba(99,102,241,0.06);
            color: #6366f1;
            font-weight: 500;
        }
        .check-item input { margin: 0; accent-color: #6366f1; }

        .radio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.5rem;
        }
        .radio-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: border-color 0.15s, background 0.15s;
        }
        .radio-item:has(input:checked) {
            border-color: #6366f1;
            background: rgba(99,102,241,0.06);
            color: #6366f1;
            font-weight: 500;
        }
        .radio-item input { margin: 0; accent-color: #6366f1; }

        .submit-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .submit-bar p { font-size: 0.82rem; color: var(--text-light, #6b7280); margin: 0; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.9; }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .error-msg { color: #ef4444; font-size: 0.8rem; margin-top: 0.3rem; }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border-radius: 8px;
            padding: 0.25rem 0.65rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .revenue-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.5rem;
        }
    </style>
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
                    <li><a href="{{ url('/') }}"><i class="fas fa-home" style="margin-right:0.3rem;"></i>Home</a></li>
                    <li><a href="{{ route('client.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('client.insights.index') }}" class="active">Insights</a></li>
                </ul>
                <div class="nav-controls">
                    <button class="hamburger" aria-label="Toggle menu"><span></span><span></span><span></span></button>
                    <button id="darkModeToggle" class="dark-mode-btn" aria-label="Toggle dark mode"><i class="fas fa-moon"></i></button>
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
                <div class="personalize-wrap">

                    <div class="step-header">
                        <div class="step-icon"><i class="fas fa-magic"></i></div>
                        <h1>Personalisasi <span style="color:#6366f1;">Bisnis Anda</span></h1>
                        <p>Semakin lengkap informasi yang Anda isi, semakin akurat diagnosis dan rekomendasi strategi konten dari AI.</p>
                    </div>

                    @if ($errors->any())
                        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:12px;padding:1rem 1.2rem;margin-bottom:1.25rem;">
                            <p style="font-size:0.85rem;font-weight:600;color:#dc2626;margin:0 0 0.4rem;"><i class="fas fa-exclamation-circle" style="margin-right:0.3rem;"></i>Mohon perbaiki kesalahan berikut:</p>
                            <ul style="margin:0;padding-left:1.2rem;">
                                @foreach ($errors->all() as $error)
                                    <li style="font-size:0.83rem;color:#b91c1c;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('client.insights.personalize.submit') }}" id="personalizeForm">
                        @csrf
                        @php $pa = $client->personalization_answers ?? []; @endphp

                        {{-- ═══════════════════════════════════════════════════════ --}}
                        {{-- SECTION 1: IDENTITAS BISNIS                           --}}
                        {{-- ═══════════════════════════════════════════════════════ --}}
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-building" style="margin-right:0.4rem;"></i>Identitas Bisnis</div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="company_name">Nama Bisnis / Brand</label>
                                    <input type="text" id="company_name" name="company_name"
                                           value="{{ old('company_name', $client->company_name) }}"
                                           placeholder="Contoh: Dapur Nusantara" required maxlength="100">
                                    @error('company_name')<p class="error-msg">{{ $message }}</p>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="industry">Industri / Kategori Bisnis</label>
                                    <select id="industry" name="industry" required>
                                        <option value="">-- Pilih industri --</option>
                                        @foreach ([
                                            'food'       => 'Food & Beverage (Kuliner)',
                                            'fashion'    => 'Fashion & Apparel',
                                            'beauty'     => 'Beauty & Skincare',
                                            'technology' => 'Technology & Startup',
                                            'education'  => 'Education & E-learning',
                                            'health'     => 'Health & Wellness',
                                            'property'   => 'Property & Real Estate',
                                            'retail'     => 'Retail & Online Shop',
                                            'finance'    => 'Finance & Investment',
                                            'creative'   => 'Creative & Digital Agency',
                                        ] as $val => $label)
                                            <option value="{{ $val }}"
                                                @selected(old('industry', $client->industry) === $val)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('industry')<p class="error-msg">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi Bisnis & Produk / Layanan</label>
                                <textarea id="description" name="description"
                                          placeholder="Ceritakan bisnis Anda: apa yang dijual, untuk siapa, dan bagaimana cara kerjanya."
                                          required maxlength="500">{{ old('description', $pa['description'] ?? '') }}</textarea>
                                @error('description')<p class="error-msg">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- ═══════════════════════════════════════════════════════ --}}
                        {{-- SECTION 2: PRODUK UNGGULAN                            --}}
                        {{-- ═══════════════════════════════════════════════════════ --}}
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-box-open" style="margin-right:0.4rem;"></i>Produk Unggulan</div>

                            <div class="form-group">
                                <label for="product_featured">Produk / Layanan Unggulan Anda</label>
                                <textarea id="product_featured" name="product_featured"
                                          placeholder="Contoh: Jersey futsal custom full-print, bahan drifit premium. Bisa pesan dari 1 pcs, ready dalam 7 hari."
                                          maxlength="400" rows="3">{{ old('product_featured', $pa['product_featured'] ?? '') }}</textarea>
                                <p class="form-hint"><i class="fas fa-info-circle" style="color:#6366f1;margin-right:0.2rem;"></i>Sebutkan produk/layanan yang paling sering dibeli atau yang ingin Anda fokuskan.</p>
                            </div>

                            <div class="form-group">
                                <label for="product_usp">USP — Unique Selling Point</label>
                                <textarea id="product_usp" name="product_usp"
                                          placeholder="Contoh: Satu-satunya toko jersey di kota ini yang bisa custom 1 pcs tanpa minimum order, dengan desainer in-house."
                                          maxlength="300" rows="2">{{ old('product_usp', $pa['product_usp'] ?? '') }}</textarea>
                                <p class="form-hint">Apa yang membuat produk Anda BERBEDA dan tidak mudah ditiru kompetitor?</p>
                            </div>

                            <div class="form-group">
                                <label for="product_advantages">Kelebihan Produk Anda</label>
                                <textarea id="product_advantages" name="product_advantages"
                                          placeholder="Contoh: Kualitas bahan terjamin, harga lebih terjangkau dari brand lain, pengiriman cepat, after-sales service bagus."
                                          maxlength="300" rows="2">{{ old('product_advantages', $pa['product_advantages'] ?? '') }}</textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="production_capacity">Kapasitas Produksi / Stok</label>
                                    <input type="text" id="production_capacity" name="production_capacity"
                                           value="{{ old('production_capacity', $pa['production_capacity'] ?? '') }}"
                                           placeholder="Contoh: 50 pcs/minggu, atau stok 200 pcs ready"
                                           maxlength="150">
                                    <p class="form-hint">Berapa banyak yang bisa Anda produksi atau layani per periode?</p>
                                </div>

                                <div class="form-group">
                                    <label for="years_in_market">Sudah Berapa Lama di Market?</label>
                                    <select id="years_in_market" name="years_in_market">
                                        <option value="">-- Pilih --</option>
                                        @foreach ([
                                            'baru'    => 'Baru mulai (< 6 bulan)',
                                            '6-12'    => '6–12 bulan',
                                            '1-2'     => '1–2 tahun',
                                            '2-5'     => '2–5 tahun',
                                            '5+'      => 'Lebih dari 5 tahun',
                                        ] as $val => $label)
                                            <option value="{{ $val }}"
                                                @selected(old('years_in_market', $pa['years_in_market'] ?? '') === $val)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- ═══════════════════════════════════════════════════════ --}}
                        {{-- SECTION 3: PROFIL OWNER                               --}}
                        {{-- ═══════════════════════════════════════════════════════ --}}
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-user-tie" style="margin-right:0.4rem;"></i>Profil Owner</div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Gender Owner</label>
                                    <div class="radio-grid" style="grid-template-columns:repeat(3,1fr);">
                                        @php $savedGender = old('owner_gender', $pa['owner_gender'] ?? ''); @endphp
                                        @foreach (['Pria', 'Wanita', 'Lainnya'] as $g)
                                            <label class="radio-item">
                                                <input type="radio" name="owner_gender" value="{{ $g }}"
                                                       @checked($savedGender === $g)>
                                                {{ $g }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="owner_age">Rentang Usia Owner</label>
                                    <select id="owner_age" name="owner_age">
                                        <option value="">-- Pilih --</option>
                                        @foreach ([
                                            '18-24' => '18–24 tahun',
                                            '25-30' => '25–30 tahun',
                                            '31-35' => '31–35 tahun',
                                            '36-45' => '36–45 tahun',
                                            '46+'   => '46 tahun ke atas',
                                        ] as $val => $label)
                                            <option value="{{ $val }}"
                                                @selected(old('owner_age', $pa['owner_age'] ?? '') === $val)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="owner_personality">Karakteristik & Kepribadian Owner</label>
                                <textarea id="owner_personality" name="owner_personality"
                                          placeholder="Contoh: Orangnya ramah, suka ngobrol, perfeksionis soal kualitas produk, lebih suka kerja di balik layar daripada tampil di depan kamera."
                                          maxlength="400" rows="3">{{ old('owner_personality', $pa['owner_personality'] ?? '') }}</textarea>
                                <p class="form-hint">Deskripsikan kepribadian owner secara jujur — ini membantu AI menyesuaikan gaya konten yang natural.</p>
                            </div>

                            <div class="form-group">
                                <label for="owner_values">Value / Prinsip Berbisnis</label>
                                <textarea id="owner_values" name="owner_values"
                                          placeholder="Contoh: Jujur soal kualitas, tidak mau overselling, prioritas kepuasan pelanggan daripada profit cepat."
                                          maxlength="300" rows="2">{{ old('owner_values', $pa['owner_values'] ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="owner_habits">Habit / Kebiasaan Berbisnis</label>
                                <textarea id="owner_habits" name="owner_habits"
                                          placeholder="Contoh: Selalu balas chat sendiri, sering posting behind the scenes di story, aktif di komunitas bisnis online."
                                          maxlength="300" rows="2">{{ old('owner_habits', $pa['owner_habits'] ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Gaya Bicara / Berkomunikasi</label>
                                <div class="radio-grid">
                                    @php $savedCommStyle = old('owner_comm_style', $pa['owner_comm_style'] ?? ''); @endphp
                                    @foreach ([
                                        'santai'      => 'Santai & Akrab',
                                        'formal'      => 'Formal & Profesional',
                                        'humoris'     => 'Humoris & Fun',
                                        'informatif'  => 'Informatif & Edukatif',
                                        'inspiratif'  => 'Inspiratif & Motivatif',
                                    ] as $val => $label)
                                        <label class="radio-item">
                                            <input type="radio" name="owner_comm_style" value="{{ $val }}"
                                                   @checked($savedCommStyle === $val)>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="owner_interests">Kesukaan / Hobi Owner <span class="opt">(opsional)</span></label>
                                <input type="text" id="owner_interests" name="owner_interests"
                                       value="{{ old('owner_interests', $pa['owner_interests'] ?? '') }}"
                                       placeholder="Contoh: Sepak bola, memasak, traveling, photography"
                                       maxlength="200">
                                <p class="form-hint">Hobi bisa menjadi angle konten personal yang menarik dan autentik.</p>
                            </div>
                        </div>

                        {{-- ═══════════════════════════════════════════════════════ --}}
                        {{-- SECTION 4: PROFIL CUSTOMER                            --}}
                        {{-- ═══════════════════════════════════════════════════════ --}}
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-users" style="margin-right:0.4rem;"></i>Profil Customer</div>

                            <div class="form-group">
                                <label for="target_audience">Target Audiens (Gambaran Umum)</label>
                                <textarea id="target_audience" name="target_audience"
                                          placeholder="Contoh: Pelaku olahraga futsal usia 17–35 tahun, aktif di komunitas olahraga, budget terbatas namun peduli tampilan tim."
                                          required maxlength="300">{{ old('target_audience', $pa['target_audience'] ?? '') }}</textarea>
                                @error('target_audience')<p class="error-msg">{{ $message }}</p>@enderror
                            </div>

                            <div class="form-group">
                                <label for="customer_buy_reason">Mengapa Customer Pertama Kali Membeli?</label>
                                <textarea id="customer_buy_reason" name="customer_buy_reason"
                                          placeholder="Contoh: Karena harganya lebih murah dari toko offline, bisa custom desain sendiri, dan lihat review positif di story Instagram."
                                          maxlength="400" rows="3">{{ old('customer_buy_reason', $pa['customer_buy_reason'] ?? '') }}</textarea>
                                <p class="form-hint">Apa trigger utama yang membuat orang baru mau mencoba produk Anda pertama kali?</p>
                            </div>

                            <div class="form-group">
                                <label for="customer_repeat_reason">Mengapa Customer Beli Lagi (Repeat Order)?</label>
                                <textarea id="customer_repeat_reason" name="customer_repeat_reason"
                                          placeholder="Contoh: Kualitas produk konsisten, pelayanan ramah dan responsif, ada diskon untuk pelanggan lama, mudah order ulang."
                                          maxlength="400" rows="3">{{ old('customer_repeat_reason', $pa['customer_repeat_reason'] ?? '') }}</textarea>
                                <p class="form-hint">Apa yang membuat mereka kembali lagi? Faktor apa yang membangun loyalitas?</p>
                            </div>

                            <div class="form-group">
                                <label for="customer_need_reason">Mengapa Customer Benar-benar Butuh Produk Ini?</label>
                                <textarea id="customer_need_reason" name="customer_need_reason"
                                          placeholder="Contoh: Mereka butuh jersey yang bagus untuk tampil kompak saat turnamen tapi tidak mau keluar banyak uang. Jersey custom juga menambah rasa bangga dan identitas tim."
                                          maxlength="400" rows="3">{{ old('customer_need_reason', $pa['customer_need_reason'] ?? '') }}</textarea>
                                <p class="form-hint">Pain point atau kebutuhan mendasar apa yang produk Anda selesaikan?</p>
                            </div>

                            <div class="form-group">
                                <label for="customer_persona">Customer Persona Lengkap <span class="opt">(opsional)</span></label>
                                <textarea id="customer_persona" name="customer_persona"
                                          placeholder="Contoh: Budi, 22 tahun, mahasiswa, suka futsal 2x seminggu dengan teman kampus. Budget beli jersey maks 150rb. Sering scroll TikTok malam hari, percaya rekomendasi dari konten creator olahraga."
                                          maxlength="500" rows="3">{{ old('customer_persona', $pa['customer_persona'] ?? '') }}</textarea>
                                <p class="form-hint">Buat 1 karakter fiktif yang mewakili pelanggan ideal Anda — nama, usia, pekerjaan, kebiasaan, dan cara mereka menemukan produk Anda.</p>
                            </div>
                        </div>

                        {{-- ═══════════════════════════════════════════════════════ --}}
                        {{-- SECTION 5: TARGET & TUJUAN                            --}}
                        {{-- ═══════════════════════════════════════════════════════ --}}
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-bullseye" style="margin-right:0.4rem;"></i>Target & Tujuan Media Sosial</div>

                            <div class="form-group">
                                <label>Tujuan Media Sosial <span class="opt">(pilih semua yang sesuai)</span></label>
                                <div class="checkbox-grid">
                                    @php $savedGoals = old('goals', $pa['goals'] ?? []); @endphp
                                    @foreach ([
                                        'brand_awareness' => 'Brand Awareness',
                                        'penjualan'       => 'Tingkatkan Penjualan',
                                        'engagement'      => 'Bangun Komunitas',
                                        'edukasi'         => 'Edukasi Audiens',
                                        'traffic'         => 'Drive Traffic Website',
                                        'rekrutmen'       => 'Rekrutmen / HR',
                                    ] as $val => $label)
                                        <label class="check-item">
                                            <input type="checkbox" name="goals[]" value="{{ $val }}"
                                                   @checked(in_array($val, (array) $savedGoals))>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('goals')<p class="error-msg">{{ $message }}</p>@enderror
                            </div>

                            <div class="form-group">
                                <label>Platform yang Digunakan <span class="opt">(pilih semua yang aktif)</span></label>
                                <div class="checkbox-grid">
                                    @php $savedPlatforms = old('platforms', $pa['platforms'] ?? []); @endphp
                                    @foreach ([
                                        'instagram' => '<i class="fab fa-instagram" style="color:#e1306c;"></i> Instagram',
                                        'tiktok'    => '<i class="fab fa-tiktok"></i> TikTok',
                                        'facebook'  => '<i class="fab fa-facebook" style="color:#1877f2;"></i> Facebook',
                                        'youtube'   => '<i class="fab fa-youtube" style="color:#ff0000;"></i> YouTube',
                                    ] as $val => $label)
                                        <label class="check-item">
                                            <input type="checkbox" name="platforms[]" value="{{ $val }}"
                                                   @checked(in_array($val, (array) $savedPlatforms))>
                                            {!! $label !!}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Gaya / Tone Konten yang Diinginkan</label>
                                <div class="radio-grid">
                                    @php $savedTone = old('content_tone', $pa['content_tone'] ?? ''); @endphp
                                    @foreach ([
                                        'informatif'   => 'Informatif / Edukatif',
                                        'entertaining' => 'Hiburan / Fun',
                                        'inspiratif'   => 'Inspiratif',
                                        'promotional'  => 'Promosi / Sales',
                                        'storytelling' => 'Storytelling',
                                    ] as $val => $label)
                                        <label class="radio-item">
                                            <input type="radio" name="content_tone" value="{{ $val }}"
                                                   @checked($savedTone === $val)>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('content_tone')<p class="error-msg">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- ═══════════════════════════════════════════════════════ --}}
                        {{-- SECTION 6: BISNIS & KEUANGAN                          --}}
                        {{-- ═══════════════════════════════════════════════════════ --}}
                        <div class="form-section">
                            <div class="form-section-title"><i class="fas fa-chart-bar" style="margin-right:0.4rem;"></i>Bisnis & Anggaran</div>

                            <div class="form-group">
                                <label>Estimasi Omzet per Bulan</label>
                                <div class="revenue-options">
                                    @php $savedRevenue = old('monthly_revenue', $pa['monthly_revenue'] ?? ''); @endphp
                                    @foreach ([
                                        '<5jt'      => '< Rp 5 juta',
                                        '5-10jt'    => 'Rp 5–10 juta',
                                        '10-25jt'   => 'Rp 10–25 juta',
                                        '25-50jt'   => 'Rp 25–50 juta',
                                        '50-100jt'  => 'Rp 50–100 juta',
                                        '>100jt'    => '> Rp 100 juta',
                                    ] as $val => $label)
                                        <label class="radio-item">
                                            <input type="radio" name="monthly_revenue" value="{{ $val }}"
                                                   @checked($savedRevenue === $val)>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                <p class="form-hint"><i class="fas fa-shield-alt" style="color:#6366f1;margin-right:0.2rem;"></i>Data ini bersifat rahasia dan hanya digunakan untuk menentukan rekomendasi budget iklan Meta/TikTok yang realistis.</p>
                            </div>

                            <div class="form-group">
                                <label for="competitors">Kompetitor yang Diketahui <span class="opt">(opsional)</span></label>
                                <textarea id="competitors" name="competitors"
                                          placeholder="Contoh: @jerseymurah.id, Toko X di shopee — sebutkan nama akun atau brand yang bersaing langsung dengan Anda."
                                          maxlength="300" rows="2">{{ old('competitors', $pa['competitors'] ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="unique_value">Keunggulan / Nilai Unik Bisnis <span class="opt">(opsional)</span></label>
                                <textarea id="unique_value" name="unique_value"
                                          placeholder="Contoh: Pengiriman same-day, bahan organik bersertifikat, garansi refund 30 hari."
                                          maxlength="300" rows="2">{{ old('unique_value', $pa['unique_value'] ?? '') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="customer_testimonials">Testimoni / Feedback Pelanggan <span class="opt">(opsional)</span></label>
                                <textarea id="customer_testimonials" name="customer_testimonials"
                                          placeholder="Contoh: &quot;Jerseynya keren banget, bahan adem, tim kami langsung percaya diri tampil di turnamen!&quot;"
                                          maxlength="500" rows="3">{{ old('customer_testimonials', $pa['customer_testimonials'] ?? '') }}</textarea>
                                <p class="form-hint"><i class="fas fa-info-circle" style="color:#6366f1;margin-right:0.2rem;"></i>Testimoni nyata membantu AI memahami sudut pandang pelanggan secara akurat.</p>
                            </div>
                        </div>

                        <div class="submit-bar">
                            <p><i class="fas fa-robot" style="color:#6366f1;margin-right:0.3rem;"></i>AI akan menganalisis semua jawaban Anda dan membuat diagnosis brand + rekomendasi strategi konten.</p>
                            <button type="submit" class="btn-primary" id="submitBtn">
                                <i class="fas fa-magic"></i> Buat Rekomendasi AI
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </section>
    </main>

    <script src="/script.js"></script>
    <script>
        document.getElementById('personalizeForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menganalisis...';
        });
    </script>
</body>
</html>
