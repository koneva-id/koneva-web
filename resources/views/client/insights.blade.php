<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insights | Koneva</title>
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .post-card {
            border-radius: 12px;
            overflow: hidden;
            background: var(--card-bg, #fff);
            border: 1px solid var(--border, #e5e7eb);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .post-thumb {
            width: 100%;
            aspect-ratio: 1;
            background: var(--border, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #9ca3af;
            overflow: hidden;
            position: relative;
        }
        .post-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .post-type-badge {
            position: absolute;
            top: 6px; right: 6px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            border-radius: 6px;
            padding: 2px 7px;
            font-size: 0.72rem;
        }
        .post-meta {
            padding: 0.5rem 0.7rem 0.3rem;
            font-size: 0.82rem;
            color: var(--text-light, #6b7280);
            display: flex;
            gap: 0.75rem;
        }
        .post-caption {
            padding: 0 0.7rem 0.6rem;
            font-size: 0.78rem;
            color: var(--text, #374151);
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* Top posts */
        .top-posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-top: 1rem;
        }
        .top-post-card {
            border-radius: 14px;
            overflow: hidden;
            background: var(--card-bg, #fff);
            border: 2px solid rgba(99,102,241,0.25);
            transition: transform 0.2s;
        }
        .top-post-card:hover { transform: translateY(-4px); }
        .top-post-thumb {
            width: 100%;
            aspect-ratio: 4/3;
            background: var(--border, #e5e7eb);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .top-post-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .rank-badge {
            position: absolute;
            top: 8px; left: 8px;
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #6366f1;
            color: #fff;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center;
        }
        .top-post-stats {
            padding: 0.65rem 0.85rem;
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--text-light, #6b7280);
            font-weight: 500;
        }
        .stat-val { font-weight: 700; color: var(--text, #374151); }

        /* Hashtag group */
        .hashtag-section { margin-bottom: 0.5rem; }
        .hashtag-label {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(99,102,241,0.1);
            color: #6366f1;
            border-radius: 999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 0.6rem;
        }

        /* Similar accounts */
        .similar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .similar-card {
            border-radius: 14px;
            padding: 1.25rem 0.85rem;
            background: var(--card-bg, #fff);
            border: 1px solid var(--border, #e5e7eb);
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .similar-card:hover { transform: translateY(-3px); box-shadow: 0 4px 16px rgba(0,0,0,0.07); }
        .similar-avatar {
            width: 60px; height: 60px;
            border-radius: 50%;
            margin: 0 auto 0.6rem;
            background: var(--border, #e5e7eb);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; overflow: hidden;
        }
        .similar-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .similar-username { font-weight: 600; font-size: 0.82rem; word-break: break-all; }
        .similar-name { font-size: 0.75rem; color: var(--text-light, #6b7280); margin-top: 0.2rem; }
        .platform-tt { color: #010101; }
        .platform-ig { color: #e1306c; }

        /* Industry selector */
        .industry-form {
            display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-top: 0.75rem;
        }
        .industry-form select {
            padding: 0.45rem 0.75rem;
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 8px;
            font-size: 0.88rem;
            background: var(--card-bg, #fff);
            color: var(--text, #374151);
        }
        .account-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: var(--card-bg, #fff);
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 999px;
            padding: 0.3rem 0.8rem;
            font-size: 0.84rem; font-weight: 500;
        }
        .connect-form {
            display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: flex-end; margin-top: 1rem;
        }
        .connect-form select, .connect-form input {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border, #e5e7eb);
            border-radius: 8px; font-size: 0.9rem;
            background: var(--card-bg, #fff);
            color: var(--text, #374151);
        }
        .empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--text-light, #6b7280); }
        .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.4; display: block; }
        .sync-info { font-size: 0.76rem; color: var(--text-light, #6b7280); }
        .section-divider { margin: 1.75rem 0 0; border: none; border-top: 1px solid var(--border, #e5e7eb); }
        .subsection-title { font-size: 1rem; font-weight: 600; margin: 1.25rem 0 0; }
        .hashtag-pill {
            display: inline-block;
            background: rgba(99,102,241,0.08);
            color: #6366f1;
            border-radius: 999px;
            padding: 0.12rem 0.5rem;
            font-size: 0.73rem;
            margin: 0.1rem;
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
                    <li><a href="{{ route('client.requests.index') }}">Requests</a></li>
                    <li><a href="{{ route('client.deliverables.index') }}">Deliverables</a></li>
                    <li><a href="{{ route('client.insights.index') }}" class="active">Insights</a></li>
                    <li><a href="{{ route('profile.settings') }}">Profile</a></li>
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

                @if (session('status'))
                    <div class="auth-status show" style="margin-bottom:1.25rem;padding:0.65rem 1rem;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:12px;font-size:0.95rem;">
                        <i class="fas fa-check-circle" style="margin-right:0.5rem;"></i>{{ session('status') }}
                    </div>
                @endif

                <div class="section-header">
                    <span class="section-tag">Business Insights</span>
                    <h1>Personalisasi <span>Bisnis</span></h1>
                    <p>Analisis konten sosial media Anda dan temukan akun serupa di industri yang sama.</p>
                </div>

                {{-- ── 1. Akun Terhubung ──────────────────────────────────────── --}}
                <div class="portal-card" style="margin-bottom:1.5rem;">
                    <h3 style="margin:0 0 0.75rem;">Akun Terhubung</h3>

                    @forelse ($accounts as $account)
                        @php $analysis = $account->ai_analysis; @endphp
                        <div style="border:1px solid var(--border,#e5e7eb);border-radius:14px;overflow:hidden;margin-bottom:1rem;">
                            {{-- Account header --}}
                            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;padding:0.85rem 1rem;background:var(--card-bg,#fff);">
                                <span class="account-badge">
                                    @if ($account->platform === 'instagram')
                                        <i class="fab fa-instagram platform-ig"></i>
                                    @else
                                        <i class="fab fa-tiktok platform-tt"></i>
                                    @endif
                                    {{ $account->username }}
                                </span>
                                @if ($account->follower_count)
                                    <span style="font-size:0.8rem;color:var(--text-light);">{{ number_format($account->follower_count) }} followers</span>
                                @endif
                                @if ($analysis)
                                    @php $grade = $analysis['grade'] ?? '–'; $score = $analysis['overall_score'] ?? 0; @endphp
                                    <span style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.8rem;font-weight:700;background:{{ $score >= 80 ? 'rgba(16,185,129,0.12)' : ($score >= 60 ? 'rgba(99,102,241,0.1)' : 'rgba(234,179,8,0.1)') }};color:{{ $score >= 80 ? '#059669' : ($score >= 60 ? '#4f46e5' : '#ca8a04') }};border-radius:8px;padding:0.2rem 0.55rem;">
                                        <i class="fas fa-star"></i> Skor {{ $score }}/100 &middot; {{ $grade }}
                                    </span>
                                @endif
                                <span class="sync-info" style="margin-left:auto;">Sync: {{ $account->last_synced_at?->diffForHumans() ?? 'Belum pernah' }}</span>
                                <form method="POST" action="{{ route('client.insights.refresh', $account) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="nav-auth-link" style="font-size:0.78rem;padding:0.3rem 0.7rem;"><i class="fas fa-sync-alt"></i> Refresh</button>
                                </form>
                                <form method="POST" action="{{ route('client.insights.analyze', $account) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="nav-auth-link" style="font-size:0.78rem;padding:0.3rem 0.7rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);"><i class="fas fa-brain"></i> {{ $analysis ? 'Analisis Ulang' : 'Analisis AI' }}</button>
                                </form>
                                <form method="POST" action="{{ route('client.insights.account-type', $account) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <label style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.75rem;color:var(--text-light);cursor:pointer;" title="Tandai sebagai akun bisnis agar postingan dipakai untuk diagnosis AI">
                                        <input type="checkbox" name="is_business_account" value="1"
                                               onchange="this.form.submit()"
                                               {{ $account->is_business_account ? 'checked' : '' }}
                                               style="accent-color:#6366f1;width:13px;height:13px;">
                                        <span style="color:{{ $account->is_business_account ? '#6366f1' : '#9ca3af' }};">
                                            {{ $account->is_business_account ? 'Akun bisnis' : 'Akun personal' }}
                                        </span>
                                    </label>
                                </form>
                                <form method="POST" action="{{ route('client.insights.disconnect', $account) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:0.78rem;"><i class="fas fa-unlink"></i> Putus</button>
                                </form>
                            </div>

                            {{-- AI Analysis panel --}}
                            @if ($analysis)
                                <div style="padding:1rem;background:linear-gradient(135deg,rgba(99,102,241,0.03),rgba(139,92,246,0.03));border-top:1px solid var(--border,#e5e7eb);">
                                    {{-- Summary --}}
                                    @if (!empty($analysis['summary']))
                                        <p style="font-size:0.85rem;color:var(--text);line-height:1.6;margin:0 0 1rem;padding:0.75rem 1rem;background:rgba(99,102,241,0.06);border-radius:10px;border-left:3px solid #6366f1;">
                                            <i class="fas fa-lightbulb" style="color:#6366f1;margin-right:0.4rem;"></i>{{ $analysis['summary'] }}
                                        </p>
                                    @endif

                                    {{-- Metrics --}}
                                    @if (!empty($analysis['metrics']))
                                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;margin-bottom:1rem;">
                                            @foreach ($analysis['metrics'] as $metric)
                                                @php $ms = (int)($metric['score'] ?? 0); @endphp
                                                <div style="background:var(--card-bg,#fff);border:1px solid var(--border,#e5e7eb);border-radius:12px;padding:0.85rem;">
                                                    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:0.4rem;">
                                                        <span style="font-size:0.82rem;font-weight:600;color:var(--text);">{{ $metric['name'] ?? '' }}</span>
                                                        <span style="font-size:1rem;font-weight:800;color:{{ $ms >= 80 ? '#059669' : ($ms >= 60 ? '#4f46e5' : ($ms >= 40 ? '#ca8a04' : '#ef4444')) }};">{{ $ms }}</span>
                                                    </div>
                                                    <div style="height:6px;background:var(--border,#e5e7eb);border-radius:3px;overflow:hidden;margin-bottom:0.45rem;">
                                                        <div style="height:100%;width:{{ $ms }}%;background:{{ $ms >= 80 ? '#10b981' : ($ms >= 60 ? '#6366f1' : ($ms >= 40 ? '#f59e0b' : '#ef4444')) }};border-radius:3px;transition:width 0.6s ease;"></div>
                                                    </div>
                                                    <div style="font-size:0.73rem;font-weight:600;color:{{ $ms >= 80 ? '#059669' : ($ms >= 60 ? '#4f46e5' : ($ms >= 40 ? '#ca8a04' : '#ef4444')) }};margin-bottom:0.3rem;">{{ $metric['label'] ?? '' }}</div>
                                                    @if (!empty($metric['explanation']))
                                                        <p style="font-size:0.75rem;color:var(--text-light);margin:0;line-height:1.4;">{{ $metric['explanation'] }}</p>
                                                    @endif
                                                    @if (!empty($metric['benchmark']))
                                                        <p style="font-size:0.72rem;color:#9ca3af;margin:0.3rem 0 0;font-style:italic;line-height:1.3;">{{ $metric['benchmark'] }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem;">
                                        {{-- Strengths --}}
                                        @if (!empty($analysis['strengths']))
                                            <div style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.2);border-radius:12px;padding:0.85rem;">
                                                <div style="font-size:0.82rem;font-weight:700;color:#065f46;margin-bottom:0.5rem;"><i class="fas fa-check-circle" style="margin-right:0.35rem;"></i>Kelebihan</div>
                                                <ul style="margin:0;padding-left:1.1rem;">
                                                    @foreach ($analysis['strengths'] as $s)
                                                        <li style="font-size:0.78rem;color:#047857;margin-bottom:0.25rem;line-height:1.4;">{{ $s }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        {{-- Content type insight --}}
                                        @if (!empty($analysis['best_post_insight']))
                                            <div style="background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.2);border-radius:12px;padding:0.85rem;">
                                                <div style="font-size:0.82rem;font-weight:700;color:#3730a3;margin-bottom:0.5rem;"><i class="fas fa-crown" style="margin-right:0.35rem;"></i>Konten Terbaik</div>
                                                <p style="font-size:0.78rem;color:#4338ca;margin:0;line-height:1.4;">{{ $analysis['best_post_insight'] }}</p>
                                                @if (!empty($analysis['content_type_breakdown']['recommendation']))
                                                    <p style="font-size:0.75rem;color:#6366f1;margin:0.4rem 0 0;font-style:italic;">{{ $analysis['content_type_breakdown']['recommendation'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Improvements --}}
                                    @if (!empty($analysis['improvements']))
                                        <div>
                                            <div style="font-size:0.82rem;font-weight:700;color:var(--text);margin-bottom:0.5rem;"><i class="fas fa-arrow-trend-up" style="color:#6366f1;margin-right:0.35rem;"></i>Yang Bisa Ditingkatkan</div>
                                            @foreach ($analysis['improvements'] as $imp)
                                                @php $pri = $imp['priority'] ?? 'Sedang'; @endphp
                                                <div style="display:flex;gap:0.65rem;align-items:flex-start;margin-bottom:0.6rem;padding:0.65rem 0.85rem;background:var(--card-bg,#fff);border:1px solid var(--border,#e5e7eb);border-radius:10px;">
                                                    <span style="flex-shrink:0;font-size:0.68rem;font-weight:700;padding:0.15rem 0.5rem;border-radius:6px;margin-top:0.1rem;background:{{ $pri === 'Tinggi' ? 'rgba(239,68,68,0.12)' : 'rgba(234,179,8,0.12)' }};color:{{ $pri === 'Tinggi' ? '#dc2626' : '#ca8a04' }};">{{ $pri }}</span>
                                                    <div>
                                                        <div style="font-size:0.82rem;font-weight:600;color:var(--text);margin-bottom:0.15rem;">{{ $imp['action'] ?? '' }}</div>
                                                        <div style="font-size:0.75rem;color:var(--text-light);line-height:1.4;">{{ $imp['reason'] ?? '' }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div style="text-align:right;margin-top:0.25rem;">
                                        <span style="font-size:0.72rem;color:#9ca3af;"><i class="fas fa-robot" style="margin-right:0.25rem;"></i>Dianalisis {{ $account->ai_analyzed_at?->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @elseif ($account->last_synced_at)
                                <div id="analysis-panel-{{ $account->id }}"
                                     style="padding:0.85rem 1rem;border-top:1px solid var(--border,#e5e7eb);background:rgba(99,102,241,0.03);"
                                     x-data="analysisPoller({{ $account->id }}, '{{ route('client.insights.analysis-status', $account) }}')"
                                     x-init="init()">

                                    {{-- idle state --}}
                                    <template x-if="!polling && !logs.length">
                                        <div style="display:flex;align-items:center;gap:0.6rem;">
                                            <i class="fas fa-brain" style="color:#a5b4fc;"></i>
                                            <span style="font-size:0.82rem;color:var(--text-light);">Analisis AI belum tersedia. Klik <strong>Analisis AI</strong> untuk mendapatkan penilaian performa akun ini.</span>
                                        </div>
                                    </template>

                                    {{-- progress log panel --}}
                                    <template x-if="polling || logs.length">
                                        <div>
                                            <div style="font-size:0.78rem;font-weight:600;color:#6366f1;margin-bottom:0.5rem;display:flex;align-items:center;gap:0.4rem;">
                                                <i class="fas fa-brain" x-bind:class="polling ? 'fa-pulse' : ''"></i>
                                                <span x-text="polling ? 'AI sedang menganalisis akun...' : 'Analisis selesai — muat ulang untuk melihat hasil'"></span>
                                            </div>
                                            <div style="background:rgba(15,15,20,0.85);border-radius:10px;padding:0.7rem 0.85rem;max-height:200px;overflow-y:auto;font-family:monospace;">
                                                <template x-for="(entry, i) in logs" :key="i">
                                                    <div style="display:flex;gap:0.5rem;margin-bottom:0.3rem;font-size:0.73rem;line-height:1.4;align-items:flex-start;">
                                                        <span style="flex-shrink:0;opacity:0.5;" x-text="entry.time"></span>
                                                        <span x-bind:style="{
                                                            color: entry.type === 'success' ? '#34d399'
                                                                 : entry.type === 'error'   ? '#f87171'
                                                                 : entry.type === 'thinking' ? '#a78bfa'
                                                                 : '#d1d5db'
                                                        }" x-text="(entry.type === 'thinking' ? '🤔 ' : entry.type === 'success' ? '✅ ' : entry.type === 'error' ? '❌ ' : '→ ') + entry.message"></span>
                                                    </div>
                                                </template>
                                                <template x-if="polling">
                                                    <div style="display:flex;gap:0.35rem;margin-top:0.25rem;font-size:0.73rem;color:#6366f1;align-items:center;">
                                                        <span class="fa-pulse" style="display:inline-block;">⚙</span>
                                                        <span>memproses...</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="font-size:0.9rem;color:var(--text-light);margin:0 0 0.75rem;">Belum ada akun yang terhubung.</p>
                    @endforelse

                    @if ($rapidApiEnabled)
                        <form method="POST" action="{{ route('client.insights.connect') }}" class="connect-form">
                            @csrf
                            <select name="platform">
                                <option value="instagram">Instagram</option>
                                <option value="tiktok">TikTok</option>
                            </select>
                            <input type="text" name="username" placeholder="username (tanpa @)"
                                   pattern="[a-zA-Z0-9._]+" maxlength="60" required value="{{ old('username') }}">
                            <label style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:var(--text-light);cursor:pointer;white-space:nowrap;">
                                <input type="checkbox" name="is_business_account" value="1" checked
                                       style="width:14px;height:14px;accent-color:#6366f1;">
                                Akun bisnis
                            </label>
                            <button type="submit" class="nav-auth-link"><i class="fas fa-plug"></i> Hubungkan</button>
                        </form>
                        @error('username')
                            <p style="color:#ef4444;font-size:0.82rem;margin-top:0.4rem;">{{ $message }}</p>
                        @enderror
                    @else
                        <div style="padding:0.65rem 1rem;background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.3);border-radius:10px;font-size:0.88rem;margin-top:0.75rem;">
                            <i class="fas fa-exclamation-triangle" style="color:#ca8a04;margin-right:0.4rem;"></i>
                            <strong>RAPIDAPI_KEY</strong> belum dikonfigurasi di <code>.env</code>.
                        </div>
                    @endif
                </div>

                {{-- ── 2. Set Industri ────────────────────────────────────────── --}}
                <div class="portal-card" style="margin-bottom:1.5rem;">
                    <h3 style="margin:0 0 0.4rem;">Industri Bisnis Anda</h3>
                    <p style="font-size:0.85rem;color:var(--text-light);margin:0 0 0.75rem;">
                        Digunakan untuk menemukan akun TikTok serupa di bidang yang sama.
                    </p>
                    <form method="POST" action="{{ route('client.insights.industry') }}" class="industry-form">
                        @csrf
                        <select name="industry">
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
                                <option value="{{ $val }}" @selected($client->industry === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="nav-auth-link"><i class="fas fa-save"></i> Simpan</button>
                    </form>
                    @if ($client->industry)
                        <p style="font-size:0.78rem;color:var(--text-light);margin-top:0.4rem;">
                            <i class="fas fa-check-circle" style="color:#10b981;margin-right:0.3rem;"></i>
                            Industri tersimpan: <strong>{{ $client->industry }}</strong>
                        </p>
                    @endif
                </div>

                {{-- ── 3. Top Performing Content ──────────────────────────────── --}}
                @if ($topPosts->isNotEmpty())
                    <div class="portal-card" style="margin-bottom:1.5rem;">
                        <h3 style="margin:0 0 0.25rem;"><i class="fas fa-trophy" style="color:#f59e0b;margin-right:0.5rem;"></i>Top Performing Content</h3>
                        <p style="font-size:0.82rem;color:var(--text-light);margin:0 0 0.25rem;">3 konten terbaik berdasarkan engagement (likes + komentar)</p>

                        <div class="top-posts-grid">
                            @foreach ($topPosts as $i => $post)
                                <div class="top-post-card">
                                    <div class="top-post-thumb">
                                        @if ($post->thumbnail_url)
                                            <img src="{{ $post->thumbnail_url }}" alt="post" loading="lazy"
                                                 onerror="this.style.display='none'">
                                        @else
                                            <i class="fas fa-image" style="font-size:2rem;opacity:0.3;"></i>
                                        @endif
                                        <span class="rank-badge">{{ $i + 1 }}</span>
                                        <span class="post-type-badge">{{ strtoupper($post->type) }}</span>
                                    </div>
                                    <div class="top-post-stats">
                                        <span><i class="fas fa-heart" style="color:#ef4444;margin-right:3px;"></i><span class="stat-val">{{ number_format($post->like_count) }}</span></span>
                                        <span><i class="fas fa-comment" style="color:#6366f1;margin-right:3px;"></i><span class="stat-val">{{ number_format($post->comment_count) }}</span></span>
                                        @if ($post->view_count)
                                            <span><i class="fas fa-eye" style="margin-right:3px;"></i><span class="stat-val">{{ number_format($post->view_count) }}</span></span>
                                        @endif
                                    </div>
                                    @if ($post->caption)
                                        <div class="post-caption">{{ $post->caption }}</div>
                                    @endif
                                    @if (!empty($post->hashtags))
                                        <div style="padding:0 0.7rem 0.6rem;">
                                            @foreach (array_slice($post->hashtags, 0, 3) as $tag)
                                                <span class="hashtag-pill">#{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── 4. Semua Konten (grouped by hashtag) ──────────────────── --}}
                @if (!empty($postsByHashtag))
                    <div class="portal-card" style="margin-bottom:1.5rem;">
                        <h3 style="margin:0 0 0.25rem;"><i class="fas fa-th-large" style="color:#6366f1;margin-right:0.5rem;"></i>Semua Konten</h3>
                        <p style="font-size:0.82rem;color:var(--text-light);margin:0;">Dikelompokkan berdasarkan topik (hashtag)</p>

                        @foreach ($postsByHashtag as $tag => $posts)
                            <hr class="section-divider">
                            <div class="hashtag-section">
                                <div class="hashtag-label">
                                    <i class="fas fa-hashtag" style="font-size:0.75rem;"></i>
                                    {{ ltrim($tag, '#') === 'Lainnya' ? 'Lainnya' : $tag }}
                                    <span style="font-weight:400;opacity:0.7;">({{ count($posts) }})</span>
                                </div>
                                <div class="insights-grid">
                                    @foreach ($posts as $post)
                                        <div class="post-card">
                                            <div class="post-thumb">
                                                @if ($post->thumbnail_url)
                                                    <img src="{{ $post->thumbnail_url }}" alt="post" loading="lazy"
                                                         onerror="this.style.display='none'">
                                                @else
                                                    <i class="fas fa-photo-video"></i>
                                                @endif
                                                <span class="post-type-badge">{{ strtoupper($post->type) }}</span>
                                            </div>
                                            <div class="post-meta">
                                                <span><i class="fas fa-heart"></i> {{ number_format($post->like_count) }}</span>
                                                <span><i class="fas fa-comment"></i> {{ number_format($post->comment_count) }}</span>
                                            </div>
                                            @if ($post->caption)
                                                <div class="post-caption">{{ $post->caption }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif ($accounts->isNotEmpty())
                    <div class="portal-card" style="margin-bottom:1.5rem;">
                        <div class="empty-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Data konten sedang diambil. Klik <strong>Refresh</strong> di akun terhubung, lalu muat ulang halaman ini.</p>
                        </div>
                    </div>
                @endif

                {{-- ── 5. AI Rekomendasi ─────────────────────────────────────── --}}
                @if ($deepseekEnabled)
                    <div class="portal-card" style="margin-bottom:1.5rem;">

                        {{-- Header --}}
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;margin-bottom:1.25rem;">
                            <div>
                                <h3 style="margin:0 0 0.25rem;display:flex;align-items:center;gap:0.5rem;">
                                    <i class="fas fa-robot" style="color:#6366f1;"></i>AI Rekomendasi
                                </h3>
                                <p style="font-size:0.82rem;color:var(--text-light);margin:0;">
                                    Dianalisis oleh DeepSeek AI berdasarkan profil bisnis Anda &mdash; cache 24 jam
                                </p>
                            </div>
                            <form method="POST" action="{{ route('client.insights.ai-refresh') }}">
                                @csrf
                                <button type="submit" class="nav-auth-link" style="font-size:0.82rem;white-space:nowrap;">
                                    <i class="fas fa-sync-alt"></i> Perbarui AI
                                </button>
                            </form>
                        </div>

                        @if ($aiRecommendation)

                            {{-- ① Brand DNA Card --}}
                            @if (!empty($aiRecommendation['brand_dna']))
                                @php
                                    $dna         = $aiRecommendation['brand_dna'];
                                    $primaryKey  = $dna['primary_dna'];
                                    $primaryInfo = $dna['primary_info'] ?? [];
                                    $secondaryKey  = $dna['secondary_dna'] ?? null;
                                    $secondaryInfo = $dna['secondary_info'] ?? null;
                                    $alignment   = $dna['overall_alignment'] ?? 0;
                                    $scores      = $dna['scores'] ?? [];
                                    $strategy    = $dna['strategy'] ?? [];
                                    arsort($scores);

                                    $dnaColors = [
                                        'craftsmanship'      => ['bg'=>'rgba(146,64,14,0.08)','border'=>'rgba(146,64,14,0.25)','text'=>'#92400e','badge_bg'=>'#fef3c7','badge_text'=>'#92400e'],
                                        'community'          => ['bg'=>'rgba(6,95,70,0.07)','border'=>'rgba(6,95,70,0.25)','text'=>'#065f46','badge_bg'=>'#d1fae5','badge_text'=>'#065f46'],
                                        'innovation'         => ['bg'=>'rgba(29,78,216,0.07)','border'=>'rgba(29,78,216,0.25)','text'=>'#1d4ed8','badge_bg'=>'#dbeafe','badge_text'=>'#1d4ed8'],
                                        'authority'          => ['bg'=>'rgba(30,58,95,0.07)','border'=>'rgba(30,58,95,0.25)','text'=>'#1e3a5f','badge_bg'=>'#e0f2fe','badge_text'=>'#1e3a5f'],
                                        'lifestyle'          => ['bg'=>'rgba(124,58,237,0.07)','border'=>'rgba(124,58,237,0.25)','text'=>'#7c3aed','badge_bg'=>'#ede9fe','badge_text'=>'#7c3aed'],
                                        'value_driven'       => ['bg'=>'rgba(4,120,87,0.07)','border'=>'rgba(4,120,87,0.25)','text'=>'#047857','badge_bg'=>'#d1fae5','badge_text'=>'#047857'],
                                        'efficiency'         => ['bg'=>'rgba(180,83,9,0.07)','border'=>'rgba(180,83,9,0.25)','text'=>'#b45309','badge_bg'=>'#fef3c7','badge_text'=>'#b45309'],
                                        'personal_connection'=> ['bg'=>'rgba(190,24,93,0.07)','border'=>'rgba(190,24,93,0.25)','text'=>'#be185d','badge_bg'=>'#fce7f3','badge_text'=>'#be185d'],
                                        'local_pride'        => ['bg'=>'rgba(220,38,38,0.07)','border'=>'rgba(220,38,38,0.25)','text'=>'#dc2626','badge_bg'=>'#fee2e2','badge_text'=>'#dc2626'],
                                        'transformation'     => ['bg'=>'rgba(107,33,168,0.07)','border'=>'rgba(107,33,168,0.25)','text'=>'#6b21a8','badge_bg'=>'#f3e8ff','badge_text'=>'#6b21a8'],
                                    ];
                                    $pc = $dnaColors[$primaryKey] ?? $dnaColors['craftsmanship'];
                                @endphp
                                <div style="border:2px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};border-radius:16px;padding:1.3rem 1.4rem;margin-bottom:1.5rem;">
                                    {{-- Header --}}
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.1rem;">
                                        <div>
                                            <div style="font-size:0.7rem;font-weight:700;color:{{ $pc['text'] }};text-transform:uppercase;letter-spacing:0.07em;margin-bottom:0.4rem;">
                                                <i class="fas fa-dna" style="margin-right:0.3rem;"></i>Brand DNA · Koneva Engine
                                            </div>
                                            <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
                                                <span style="font-size:1.45rem;font-weight:800;color:{{ $pc['text'] }};">
                                                    <i class="fas {{ $primaryInfo['icon'] ?? 'fa-gem' }}" style="margin-right:0.4rem;font-size:1.1rem;"></i>{{ $primaryInfo['name'] ?? $primaryKey }}
                                                </span>
                                                <span style="background:{{ $pc['badge_bg'] }};color:{{ $pc['badge_text'] }};font-size:0.72rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:20px;">
                                                    Skor {{ $scores[$primaryKey] ?? 0 }}/100
                                                </span>
                                                @if ($secondaryKey && $secondaryInfo)
                                                    @php $sc = $dnaColors[$secondaryKey] ?? $dnaColors['community']; @endphp
                                                    <span style="background:{{ $sc['badge_bg'] }};color:{{ $sc['badge_text'] }};font-size:0.72rem;font-weight:600;padding:0.25rem 0.65rem;border-radius:20px;opacity:0.85;">
                                                        + {{ $secondaryInfo['name'] }} {{ $scores[$secondaryKey] ?? 0 }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p style="margin:0.35rem 0 0;font-size:0.85rem;color:{{ $pc['text'] }};opacity:0.8;">{{ $primaryInfo['character'] ?? '' }}</p>
                                        </div>
                                        <div style="text-align:right;min-width:90px;">
                                            <div style="font-size:0.68rem;color:{{ $pc['text'] }};opacity:0.7;margin-bottom:0.2rem;">Alignment Score</div>
                                            <div style="font-size:2rem;font-weight:800;color:{{ $pc['text'] }};line-height:1;">{{ $alignment }}</div>
                                            <div style="font-size:0.68rem;color:{{ $pc['text'] }};opacity:0.7;">/100</div>
                                        </div>
                                    </div>

                                    {{-- DNA Score Bars --}}
                                    <div style="margin-bottom:1rem;">
                                        <div style="font-size:0.7rem;font-weight:700;color:{{ $pc['text'] }};text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.6rem;">Peta DNA (10 Dimensi)</div>
                                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.3rem 1.5rem;">
                                            @foreach ($scores as $dnaKey => $dnaScore)
                                                @php
                                                    $info  = \App\Services\BrandDna\DnaTaxonomy::DNA_TYPES[$dnaKey] ?? null;
                                                    $dc    = $dnaColors[$dnaKey] ?? $dnaColors['craftsmanship'];
                                                    $isTop = $dnaKey === $primaryKey || $dnaKey === $secondaryKey;
                                                @endphp
                                                @if ($info)
                                                    <div style="display:flex;align-items:center;gap:0.5rem;padding:0.2rem 0;">
                                                        <div style="font-size:0.72rem;color:var(--text);width:130px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:{{ $isTop ? '700' : '400' }};color:{{ $isTop ? $dc['text'] : 'var(--text-light)' }};">{{ $info['name_id'] }}</div>
                                                        <div style="flex:1;height:6px;background:var(--border);border-radius:3px;overflow:hidden;">
                                                            <div style="height:100%;width:{{ $dnaScore }}%;background:{{ $dc['text'] }};border-radius:3px;opacity:{{ $isTop ? '1' : '0.5' }};"></div>
                                                        </div>
                                                        <div style="font-size:0.7rem;font-weight:600;color:{{ $isTop ? $dc['text'] : 'var(--text-light)' }};width:28px;text-align:right;">{{ $dnaScore }}</div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Communication Strategy --}}
                                    @if (!empty($strategy))
                                        <div style="border-top:1px solid {{ $pc['border'] }};padding-top:0.9rem;margin-top:0.5rem;">
                                            <div style="font-size:0.7rem;font-weight:700;color:{{ $pc['text'] }};text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.65rem;">Communication Strategy Template</div>
                                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0.75rem;">
                                                {{-- Content Mix --}}
                                                @if (!empty($strategy['content_mix']))
                                                    <div>
                                                        <div style="font-size:0.7rem;font-weight:600;color:{{ $pc['text'] }};margin-bottom:0.4rem;">Konten Mix</div>
                                                        @foreach ($strategy['content_mix'] as $mix)
                                                            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">
                                                                <div style="min-width:30px;font-size:0.75rem;font-weight:700;color:{{ $pc['text'] }};">{{ $mix['pct'] }}%</div>
                                                                <div style="flex:1;height:5px;background:var(--border);border-radius:3px;overflow:hidden;">
                                                                    <div style="height:100%;width:{{ $mix['pct'] }}%;background:{{ $pc['text'] }};border-radius:3px;"></div>
                                                                </div>
                                                                <div style="font-size:0.72rem;color:var(--text-light);flex:1;">{{ $mix['label'] }}</div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                {{-- Pillars + Tone --}}
                                                <div>
                                                    @if (!empty($strategy['content_pillars']))
                                                        <div style="font-size:0.7rem;font-weight:600;color:{{ $pc['text'] }};margin-bottom:0.4rem;">Pilar Konten</div>
                                                        @foreach ($strategy['content_pillars'] as $pillar)
                                                            <div style="display:inline-block;background:{{ $pc['badge_bg'] }};color:{{ $pc['badge_text'] }};font-size:0.72rem;padding:0.2rem 0.55rem;border-radius:20px;margin:0 0.2rem 0.25rem 0;">{{ $pillar }}</div>
                                                        @endforeach
                                                    @endif
                                                    @if (!empty($strategy['hook_style']))
                                                        <div style="font-size:0.7rem;font-weight:600;color:{{ $pc['text'] }};margin:0.5rem 0 0.25rem;">Hook Opening</div>
                                                        <p style="margin:0;font-size:0.78rem;color:var(--text-light);font-style:italic;line-height:1.45;">{{ $strategy['hook_style'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- ② Content Audit --}}
                            @if (!empty($aiRecommendation['content_audit']))
                                @php $audit = $aiRecommendation['content_audit']; @endphp
                                <div style="margin-bottom:1.5rem;">
                                    <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.85rem;">
                                        <i class="fas fa-search-plus" style="color:#0891b2;font-size:1rem;"></i>
                                        <span style="font-weight:700;font-size:1rem;">Audit Konten Aktual</span>
                                        <span style="font-size:0.75rem;color:var(--text-light);font-weight:400;">· berdasarkan data postingan nyata di akun Anda</span>
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:0.85rem;">
                                        @if (!empty($audit['top_performing_pattern']))
                                            <div style="background:rgba(8,145,178,0.05);border:1px solid rgba(8,145,178,0.2);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#0e7490;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;"><i class="fas fa-fire" style="margin-right:0.3rem;color:#f59e0b;"></i>Pola Konten Terbaik</div>
                                                <p style="margin:0;font-size:0.82rem;color:#0c4a6e;line-height:1.55;">{{ $audit['top_performing_pattern'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($audit['current_gap']))
                                            <div style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.18);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;"><i class="fas fa-arrows-alt-h" style="margin-right:0.3rem;"></i>Gap Konten</div>
                                                <p style="margin:0;font-size:0.82rem;color:#b91c1c;line-height:1.55;">{{ $audit['current_gap'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($audit['owner_communication_style']))
                                            <div style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.2);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;"><i class="fas fa-comments" style="margin-right:0.3rem;"></i>Gaya Komunikasi Owner</div>
                                                <p style="margin:0;font-size:0.82rem;color:#047857;line-height:1.55;">{{ $audit['owner_communication_style'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- ② Diagnosis --}}
                            @if (!empty($aiRecommendation['diagnosis']))
                                @php $dx = $aiRecommendation['diagnosis']; @endphp
                                <div style="margin-bottom:1.75rem;">
                                    <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
                                        <i class="fas fa-stethoscope" style="color:#6366f1;font-size:1rem;"></i>
                                        <span style="font-weight:700;font-size:1rem;">Diagnosis Brand</span>
                                        <span style="font-size:0.75rem;color:var(--text-light);font-weight:400;">· analisis kondisi bisnis Anda saat ini</span>
                                    </div>

                                    {{-- Main problem + solution highlight --}}
                                    @if (!empty($dx['main_problem']))
                                        <div style="background:linear-gradient(135deg,rgba(239,68,68,0.06),rgba(249,115,22,0.04));border:1px solid rgba(239,68,68,0.2);border-left:4px solid #ef4444;border-radius:12px;padding:1rem 1.2rem;margin-bottom:1rem;">
                                            <div style="font-size:0.72rem;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.4rem;">
                                                <i class="fas fa-exclamation-circle" style="margin-right:0.3rem;"></i>Masalah Utama Brand
                                            </div>
                                            <p style="margin:0 0 0.75rem;font-size:0.9rem;color:var(--text);line-height:1.65;">{{ $dx['main_problem'] }}</p>
                                            @if (!empty($dx['solution']))
                                                <div style="font-size:0.72rem;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.35rem;">
                                                    <i class="fas fa-check-circle" style="margin-right:0.3rem;"></i>Solusi
                                                </div>
                                                <p style="margin:0;font-size:0.88rem;color:#047857;line-height:1.6;">{{ $dx['solution'] }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:0.85rem;margin-bottom:0.85rem;">
                                        {{-- Strengths --}}
                                        @if (!empty($dx['product_strengths']))
                                            <div style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.2);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;"><i class="fas fa-plus-circle" style="margin-right:0.3rem;"></i>Kelebihan Produk</div>
                                                <ul style="margin:0;padding-left:1rem;">
                                                    @foreach ($dx['product_strengths'] as $s)
                                                        <li style="font-size:0.82rem;color:#047857;margin-bottom:0.2rem;line-height:1.45;">{{ $s }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        {{-- Weaknesses --}}
                                        @if (!empty($dx['product_weaknesses']))
                                            <div style="background:rgba(234,179,8,0.05);border:1px solid rgba(234,179,8,0.2);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;"><i class="fas fa-minus-circle" style="margin-right:0.3rem;"></i>Tantangan Produk</div>
                                                <ul style="margin:0;padding-left:1rem;">
                                                    @foreach ($dx['product_weaknesses'] as $w)
                                                        <li style="font-size:0.82rem;color:#b45309;margin-bottom:0.2rem;line-height:1.45;">{{ $w }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        {{-- Market fit --}}
                                        @if (!empty($dx['market_fit']))
                                            <div style="background:rgba(99,102,241,0.05);border:1px solid rgba(99,102,241,0.2);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#3730a3;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;"><i class="fas fa-chart-line" style="margin-right:0.3rem;"></i>Daya Jual di Tren Industri</div>
                                                <p style="margin:0;font-size:0.82rem;color:#4338ca;line-height:1.5;">{{ $dx['market_fit'] }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:0.85rem;">
                                        {{-- Owner profile --}}
                                        @if (!empty($dx['owner_profile']))
                                            <div style="background:rgba(139,92,246,0.05);border:1px solid rgba(139,92,246,0.2);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#5b21b6;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.35rem;"><i class="fas fa-user-tie" style="margin-right:0.3rem;"></i>Profil Owner</div>
                                                <p style="margin:0 0 0.5rem;font-size:0.82rem;color:#6d28d9;line-height:1.5;">{{ $dx['owner_profile'] }}</p>
                                                @if (!empty($dx['owner_content_angle']))
                                                    <div style="font-size:0.72rem;font-weight:600;color:#7c3aed;margin-bottom:0.25rem;">Yang perlu ditonjolkan:</div>
                                                    <p style="margin:0;font-size:0.8rem;color:#5b21b6;line-height:1.45;font-style:italic;">{{ $dx['owner_content_angle'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                        {{-- Customer profile --}}
                                        @if (!empty($dx['customer_profile']))
                                            <div style="background:rgba(14,165,233,0.05);border:1px solid rgba(14,165,233,0.2);border-radius:12px;padding:0.9rem 1rem;">
                                                <div style="font-size:0.72rem;font-weight:700;color:#0c4a6e;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.35rem;"><i class="fas fa-users" style="margin-right:0.3rem;"></i>Profil Customer</div>
                                                <p style="margin:0 0 0.5rem;font-size:0.82rem;color:#0369a1;line-height:1.5;">{{ $dx['customer_profile'] }}</p>
                                                @if (!empty($dx['customer_content_trigger']))
                                                    <div style="font-size:0.72rem;font-weight:600;color:#0284c7;margin-bottom:0.25rem;">Yang perlu diperlihatkan di konten:</div>
                                                    <p style="margin:0;font-size:0.8rem;color:#0c4a6e;line-height:1.45;font-style:italic;">{{ $dx['customer_content_trigger'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- ② Strategy Insight --}}
                            @if (!empty($aiRecommendation['strategy_insight']))
                                <div style="background:linear-gradient(135deg,rgba(99,102,241,0.07),rgba(139,92,246,0.05));border:1px solid rgba(99,102,241,0.2);border-radius:14px;padding:1.1rem 1.3rem;margin-bottom:1.5rem;">
                                    <div style="font-size:0.75rem;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.5rem;">
                                        <i class="fas fa-lightbulb" style="margin-right:0.3rem;"></i>Insight Strategi
                                    </div>
                                    <p style="margin:0;font-size:0.9rem;color:var(--text);line-height:1.7;">{{ $aiRecommendation['strategy_insight'] }}</p>
                                </div>
                            @endif

                            {{-- Ad Budget Recommendation --}}
                            @if (!empty($aiRecommendation['ad_budget_recommendation']))
                                @php $adBudget = $aiRecommendation['ad_budget_recommendation']; @endphp
                                <div style="background:linear-gradient(135deg,rgba(16,185,129,0.06),rgba(5,150,105,0.04));border:1px solid rgba(16,185,129,0.25);border-radius:14px;padding:1.1rem 1.3rem;margin-bottom:1.5rem;">
                                    <div style="font-size:0.75rem;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.75rem;">
                                        <i class="fas fa-ad" style="margin-right:0.3rem;"></i>Rekomendasi Budget Iklan Meta / TikTok
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.75rem;">
                                        @if (!empty($adBudget['monthly_budget']))
                                            <div style="background:rgba(255,255,255,0.6);border-radius:10px;padding:0.75rem 0.85rem;">
                                                <div style="font-size:0.7rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.3rem;"><i class="fas fa-wallet" style="margin-right:0.25rem;"></i>Budget / Bulan</div>
                                                <p style="margin:0;font-size:0.85rem;color:#047857;line-height:1.5;">{{ $adBudget['monthly_budget'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($adBudget['platform_split']))
                                            <div style="background:rgba(255,255,255,0.6);border-radius:10px;padding:0.75rem 0.85rem;">
                                                <div style="font-size:0.7rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.3rem;"><i class="fas fa-chart-pie" style="margin-right:0.25rem;"></i>Alokasi Platform</div>
                                                <p style="margin:0;font-size:0.85rem;color:#047857;line-height:1.5;">{{ $adBudget['platform_split'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($adBudget['objective']))
                                            <div style="background:rgba(255,255,255,0.6);border-radius:10px;padding:0.75rem 0.85rem;">
                                                <div style="font-size:0.7rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.3rem;"><i class="fas fa-crosshairs" style="margin-right:0.25rem;"></i>Tujuan Iklan</div>
                                                <p style="margin:0;font-size:0.85rem;color:#047857;line-height:1.5;">{{ $adBudget['objective'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($adBudget['expected_result']))
                                            <div style="background:rgba(255,255,255,0.6);border-radius:10px;padding:0.75rem 0.85rem;">
                                                <div style="font-size:0.7rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.3rem;"><i class="fas fa-chart-line" style="margin-right:0.25rem;"></i>Target 30 Hari</div>
                                                <p style="margin:0;font-size:0.85rem;color:#047857;line-height:1.5;">{{ $adBudget['expected_result'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Instagram Accounts --}}
                            @if (!empty($aiRecommendation['instagram_accounts']))
                                <div style="margin-bottom:1.5rem;">
                                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.85rem;">
                                        <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);color:#fff;font-size:0.85rem;">
                                            <i class="fab fa-instagram"></i>
                                        </span>
                                        <span style="font-weight:700;font-size:1rem;">Top 3 Akun Instagram</span>
                                        <span style="font-size:0.78rem;color:var(--text-light);">dipilih dari akun nyata · badge kuning = belum diverifikasi</span>
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
                                        @foreach ($aiRecommendation['instagram_accounts'] as $acc)
                                            @php
                                                $igUsername = $acc['username'] ?? '';
                                                $igVerified = !empty($acc['ig_verified']);
                                                $igCentang  = !empty($acc['is_verified']);
                                                $igPic      = $acc['profile_pic_url'] ?? null;
                                                // Always link directly to Instagram — verified accounts confirmed real, unverified show warning badge
                                                $igUrl = 'https://instagram.com/' . $igUsername;
                                            @endphp
                                            <div style="border:1px solid var(--border,#e5e7eb);border-radius:14px;overflow:hidden;">
                                                {{-- Card header --}}
                                                <div style="padding:0.85rem 1rem 0.7rem;display:flex;align-items:center;gap:0.75rem;border-bottom:1px solid var(--border,#e5e7eb);">
                                                    {{-- Avatar: real profile pic if verified, else gradient icon --}}
                                                    <div style="width:42px;height:42px;border-radius:50%;overflow:hidden;flex-shrink:0;background:linear-gradient(135deg,#f09433,#dc2743,#bc1888);display:flex;align-items:center;justify-content:center;">
                                                        @if ($igPic)
                                                            <img src="{{ $igPic }}" alt="{{ $igUsername }}"
                                                                 style="width:100%;height:100%;object-fit:cover;"
                                                                 onerror="this.style.display='none'">
                                                        @else
                                                            <i class="fab fa-instagram" style="color:#fff;font-size:1.1rem;"></i>
                                                        @endif
                                                    </div>
                                                    <div style="min-width:0;flex:1;">
                                                        <div style="font-weight:700;font-size:0.9rem;display:flex;align-items:center;gap:0.3rem;flex-wrap:wrap;">
                                                            <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer"
                                                               style="color:var(--text);text-decoration:none;">
                                                                {{ $igUsername }}
                                                            </a>
                                                            @if ($igCentang)
                                                                <i class="fas fa-certificate" style="color:#3b82f6;font-size:0.75rem;" title="Akun terverifikasi Instagram"></i>
                                                            @endif
                                                        </div>
                                                        @if (!empty($acc['display_name']))
                                                            <div style="font-size:0.78rem;color:var(--text-light);">{{ $acc['display_name'] }}</div>
                                                        @endif
                                                        @if ($igVerified)
                                                            <span style="display:inline-flex;align-items:center;gap:0.25rem;font-size:0.68rem;background:rgba(16,185,129,0.1);color:#059669;border-radius:4px;padding:0.1rem 0.4rem;margin-top:0.2rem;">
                                                                <i class="fas fa-check-circle"></i> Akun ditemukan di Instagram
                                                            </span>
                                                        @else
                                                            <span style="display:inline-flex;align-items:center;gap:0.25rem;font-size:0.68rem;background:rgba(234,179,8,0.1);color:#ca8a04;border-radius:4px;padding:0.1rem 0.4rem;margin-top:0.2rem;">
                                                                <i class="fas fa-exclamation-triangle"></i> Saran AI — belum diverifikasi
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer"
                                                       style="margin-left:auto;font-size:0.73rem;color:#e1306c;text-decoration:none;white-space:nowrap;flex-shrink:0;align-self:flex-start;display:inline-flex;align-items:center;gap:0.25rem;">
                                                        <i class="fab fa-instagram" style="font-size:0.7rem;"></i> Lihat
                                                    </a>
                                                </div>
                                                {{-- Why follow --}}
                                                @if (!empty($acc['why_follow']))
                                                    <div style="padding:0.7rem 1rem 0.5rem;font-size:0.82rem;color:var(--text-light);line-height:1.5;border-bottom:1px solid var(--border,#e5e7eb);">
                                                        {{ $acc['why_follow'] }}
                                                    </div>
                                                @endif
                                                {{-- Reference posts --}}
                                                @if (!empty($acc['reference_posts']))
                                                    <div style="padding:0.6rem 1rem 0.85rem;">
                                                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6366f1;margin-bottom:0.5rem;">
                                                            <i class="fas fa-film" style="margin-right:0.3rem;"></i>Referensi Konten
                                                        </div>
                                                        @foreach ($acc['reference_posts'] as $post)
                                                            <div style="margin-bottom:0.6rem;padding:0.5rem 0.65rem;background:rgba(99,102,241,0.04);border-radius:8px;border-left:2px solid rgba(99,102,241,0.3);">
                                                                <div style="font-size:0.82rem;font-weight:600;color:var(--text);margin-bottom:0.2rem;">{{ $post['topic'] ?? '' }}</div>
                                                                @if (!empty($post['why_effective']))
                                                                    <div style="font-size:0.76rem;color:var(--text-light);line-height:1.45;">{{ $post['why_effective'] }}</div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- ③ TikTok Accounts --}}
                            @if (!empty($aiRecommendation['tiktok_accounts']))
                                <div style="margin-bottom:1.5rem;">
                                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.85rem;">
                                        <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:#010101;color:#fff;font-size:0.85rem;">
                                            <i class="fab fa-tiktok"></i>
                                        </span>
                                        <span style="font-weight:700;font-size:1rem;">Top 3 Akun TikTok</span>
                                        <span style="font-size:0.78rem;color:var(--text-light);">untuk dipelajari</span>
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
                                        @foreach ($aiRecommendation['tiktok_accounts'] as $acc)
                                            @php
                                                $ttUsername = $acc['username'] ?? '';
                                                $ttVerified = !empty($acc['verified']) || !empty($acc['tt_verified']);
                                                $ttUrl = 'https://tiktok.com/@' . $ttUsername;
                                            @endphp
                                            <div style="border:1px solid var(--border,#e5e7eb);border-radius:14px;overflow:hidden;">
                                                {{-- Card header --}}
                                                <div style="padding:0.85rem 1rem 0.7rem;display:flex;align-items:center;gap:0.75rem;border-bottom:1px solid var(--border,#e5e7eb);">
                                                    <div style="width:42px;height:42px;border-radius:50%;background:#010101;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                        <i class="fab fa-tiktok" style="color:#fff;font-size:1.1rem;"></i>
                                                    </div>
                                                    <div style="min-width:0;flex:1;">
                                                        <div style="font-weight:700;font-size:0.9rem;">
                                                            <a href="{{ $ttUrl }}" target="_blank" rel="noopener noreferrer"
                                                               style="color:var(--text);text-decoration:none;">
                                                                {{ $ttUsername }}
                                                            </a>
                                                        </div>
                                                        @if (!empty($acc['display_name']))
                                                            <div style="font-size:0.78rem;color:var(--text-light);">{{ $acc['display_name'] }}</div>
                                                        @endif
                                                        @if ($ttVerified)
                                                            <span style="display:inline-flex;align-items:center;gap:0.25rem;font-size:0.68rem;background:rgba(16,185,129,0.1);color:#059669;border-radius:4px;padding:0.1rem 0.4rem;margin-top:0.2rem;">
                                                                <i class="fas fa-check-circle"></i> Akun ditemukan di TikTok
                                                            </span>
                                                        @else
                                                            <span style="display:inline-flex;align-items:center;gap:0.25rem;font-size:0.68rem;background:rgba(234,179,8,0.1);color:#ca8a04;border-radius:4px;padding:0.1rem 0.4rem;margin-top:0.2rem;">
                                                                <i class="fas fa-exclamation-triangle"></i> Saran AI — belum diverifikasi
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <a href="{{ $ttUrl }}" target="_blank" rel="noopener noreferrer"
                                                       style="margin-left:auto;font-size:0.75rem;color:#6366f1;text-decoration:none;white-space:nowrap;flex-shrink:0;align-self:flex-start;">
                                                        Lihat <i class="fas fa-external-link-alt" style="font-size:0.65rem;"></i>
                                                    </a>
                                                </div>
                                                {{-- Why follow --}}
                                                @if (!empty($acc['why_follow']))
                                                    <div style="padding:0.7rem 1rem 0.5rem;font-size:0.82rem;color:var(--text-light);line-height:1.5;border-bottom:1px solid var(--border,#e5e7eb);">
                                                        {{ $acc['why_follow'] }}
                                                    </div>
                                                @endif
                                                {{-- Reference posts --}}
                                                @if (!empty($acc['reference_posts']))
                                                    <div style="padding:0.6rem 1rem 0.85rem;">
                                                        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6366f1;margin-bottom:0.5rem;">
                                                            <i class="fas fa-video" style="margin-right:0.3rem;"></i>Referensi Konten
                                                        </div>
                                                        @foreach ($acc['reference_posts'] as $post)
                                                            <div style="margin-bottom:0.6rem;padding:0.5rem 0.65rem;background:rgba(99,102,241,0.04);border-radius:8px;border-left:2px solid rgba(99,102,241,0.3);">
                                                                <div style="font-size:0.82rem;font-weight:600;color:var(--text);margin-bottom:0.2rem;">{{ $post['topic'] ?? '' }}</div>
                                                                @if (!empty($post['why_effective']))
                                                                    <div style="font-size:0.76rem;color:var(--text-light);line-height:1.45;">{{ $post['why_effective'] }}</div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- ④ Content Ideas --}}
                            @if (!empty($aiRecommendation['content_ideas']))
                                <div>
                                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.85rem;">
                                        <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:rgba(99,102,241,0.15);color:#6366f1;font-size:0.85rem;">
                                            <i class="fas fa-pen-nib"></i>
                                        </span>
                                        <span style="font-weight:700;font-size:1rem;">4 Ide Konten</span>
                                        <span style="font-size:0.78rem;color:var(--text-light);">untuk dicoba</span>
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem;">
                                        @foreach ($aiRecommendation['content_ideas'] as $idea)
                                            @php
                                                $ex     = $idea['example_post'] ?? null;
                                                $fmt    = strtolower($idea['format'] ?? 'video');
                                                $fmtIcon = match($fmt) {
                                                    'carousel'         => 'fas fa-images',
                                                    'foto', 'photo'    => 'fas fa-camera',
                                                    'reel', 'reels'    => 'fas fa-film',
                                                    default            => 'fas fa-video',
                                                };
                                                $fmtColor = match($fmt) {
                                                    'carousel'         => '#0ea5e9',
                                                    'foto', 'photo'    => '#10b981',
                                                    'reel', 'reels'    => '#ec4899',
                                                    default            => '#6366f1',
                                                };
                                                $fmtLabel = match($fmt) {
                                                    'carousel'         => 'CAROUSEL',
                                                    'foto', 'photo'    => 'FOTO',
                                                    'reel', 'reels'    => 'REELS',
                                                    default            => 'VIDEO',
                                                };
                                                $isVideo = in_array($fmt, ['video', 'reel', 'reels', 'short']);
                                            @endphp
                                            <div style="border:1px solid var(--border,#e5e7eb);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;">

                                                {{-- Thumbnail: TikTok example for video, format placeholder for others --}}
                                                @if ($ex && $isVideo)
                                                    <a href="{{ $ex['url'] }}" target="_blank" rel="noopener noreferrer"
                                                       style="display:block;position:relative;aspect-ratio:16/9;background:#111;overflow:hidden;flex-shrink:0;">
                                                        @if (!empty($ex['thumbnail']))
                                                            <img src="{{ $ex['thumbnail'] }}" alt="example"
                                                                 style="width:100%;height:100%;object-fit:cover;"
                                                                 loading="lazy" onerror="this.parentElement.style.background='#1a1a2e';this.remove();">
                                                        @endif
                                                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.55) 0%,transparent 55%);display:flex;align-items:flex-end;padding:0.5rem 0.65rem;">
                                                            <span style="color:#fff;font-size:0.72rem;font-weight:600;display:flex;align-items:center;gap:0.3rem;">
                                                                <i class="fab fa-tiktok"></i>
                                                                {{ $ex['author'] }}
                                                                @if ($ex['view_count'])
                                                                    <span style="margin-left:0.4rem;opacity:0.85;"><i class="fas fa-eye"></i> {{ number_format($ex['view_count']) }}</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div style="position:absolute;top:0.5rem;right:0.5rem;background:rgba(0,0,0,0.6);color:#fff;border-radius:6px;padding:0.2rem 0.5rem;font-size:0.68rem;font-weight:600;">
                                                            <i class="fas fa-play"></i> Lihat Contoh
                                                        </div>
                                                    </a>
                                                @else
                                                    {{-- Non-video: link to Google Images for real visual examples of this content type --}}
                                                    @php
                                                        // search_keyword may be empty string (AI leaves it blank for non-video); fall back to title
                                                        $imgKeyword = !empty($idea['search_keyword']) ? $idea['search_keyword'] : ($idea['title'] ?? '');
                                                        $imgQuery   = $imgKeyword . ' ' . $fmtLabel . ' instagram';
                                                        $imgUrl     = 'https://www.google.com/search?q=' . urlencode($imgQuery) . '&tbm=isch';
                                                    @endphp
                                                    <a href="{{ $imgUrl }}" target="_blank" rel="noopener noreferrer"
                                                       style="display:block;position:relative;aspect-ratio:16/9;background:linear-gradient(135deg,{{ $fmtColor }}20,{{ $fmtColor }}08);overflow:hidden;flex-shrink:0;border-bottom:1px solid var(--border,#e5e7eb);text-decoration:none;">
                                                        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.4rem;">
                                                            <i class="{{ $fmtIcon }}" style="font-size:2.2rem;color:{{ $fmtColor }};opacity:0.6;"></i>
                                                            <span style="font-size:0.72rem;font-weight:700;color:{{ $fmtColor }};letter-spacing:0.05em;">{{ $fmtLabel }}</span>
                                                        </div>
                                                        <div style="position:absolute;top:0.5rem;right:0.5rem;background:rgba(0,0,0,0.6);color:#fff;border-radius:6px;padding:0.2rem 0.5rem;font-size:0.68rem;font-weight:600;">
                                                            <i class="fab fa-google"></i> Cari Referensi
                                                        </div>
                                                        <div style="position:absolute;bottom:0;left:0;right:0;padding:0.4rem 0.65rem;background:linear-gradient(to top,rgba(0,0,0,0.45),transparent);">
                                                            <span style="color:#fff;font-size:0.7rem;font-weight:600;"><i class="fas fa-images" style="margin-right:0.25rem;"></i>{{ $imgKeyword }}</span>
                                                        </div>
                                                    </a>
                                                @endif

                                                {{-- Card body --}}
                                                <div style="padding:0.85rem 1rem;display:flex;flex-direction:column;gap:0.45rem;flex:1;">
                                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:0.5rem;">
                                                        <div style="font-weight:700;font-size:0.88rem;color:var(--text);line-height:1.4;">{{ $idea['title'] ?? '' }}</div>
                                                        <span style="flex-shrink:0;border-radius:6px;padding:0.15rem 0.5rem;font-size:0.68rem;font-weight:700;background:{{ $fmtColor }}18;color:{{ $fmtColor }};">
                                                            <i class="{{ $fmtIcon }}" style="margin-right:0.2rem;"></i>{{ $fmtLabel }}
                                                        </span>
                                                    </div>
                                                    @if (!empty($idea['description']))
                                                        <div style="font-size:0.8rem;color:var(--text-light);line-height:1.55;flex:1;">{{ $idea['description'] }}</div>
                                                    @endif
                                                    @if (!empty($idea['suggested_hashtags']))
                                                        <div style="display:flex;flex-wrap:wrap;gap:0.2rem;padding-top:0.15rem;">
                                                            @foreach ($idea['suggested_hashtags'] as $tag)
                                                                <span class="hashtag-pill">#{{ ltrim($tag, '#') }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if ($ex && $isVideo)
                                                        <a href="{{ $ex['url'] }}" target="_blank" rel="noopener noreferrer"
                                                           style="display:inline-flex;align-items:center;gap:0.35rem;margin-top:0.25rem;font-size:0.78rem;color:#6366f1;text-decoration:none;font-weight:600;">
                                                            <i class="fab fa-tiktok"></i> Lihat contoh video di TikTok
                                                            <i class="fas fa-external-link-alt" style="font-size:0.65rem;"></i>
                                                        </a>
                                                    @elseif (!$isVideo)
                                                        <a href="{{ $imgUrl }}" target="_blank" rel="noopener noreferrer"
                                                           style="display:inline-flex;align-items:center;gap:0.35rem;margin-top:0.25rem;font-size:0.78rem;color:#4285f4;text-decoration:none;font-weight:600;">
                                                            <i class="fab fa-google"></i> Cari referensi {{ $fmtLabel }} di Google
                                                            <i class="fas fa-external-link-alt" style="font-size:0.65rem;"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        @else
                            {{-- Live progress log --}}
                            <div id="aiProgressPanel" style="border:1px solid rgba(99,102,241,0.2);border-radius:12px;overflow:hidden;">
                                <div style="background:rgba(99,102,241,0.06);padding:0.65rem 1rem;display:flex;align-items:center;gap:0.6rem;border-bottom:1px solid rgba(99,102,241,0.15);">
                                    <i class="fas fa-circle-notch fa-spin" id="aiStatusIcon" style="color:#6366f1;font-size:0.85rem;"></i>
                                    <span style="font-size:0.85rem;font-weight:600;color:#6366f1;" id="aiStatusTitle">AI sedang menganalisis bisnis Anda…</span>
                                </div>
                                <div id="aiLogList" style="padding:0.75rem 1rem;font-size:0.82rem;font-family:monospace;max-height:260px;overflow-y:auto;display:flex;flex-direction:column;gap:0.3rem;">
                                    <span style="color:var(--text-light);">Menunggu proses dimulai…</span>
                                </div>
                                <div style="padding:0.5rem 1rem;border-top:1px solid var(--border,#e5e7eb);font-size:0.75rem;color:var(--text-light);display:flex;align-items:center;gap:0.4rem;">
                                    <i class="fas fa-info-circle"></i>
                                    Proses ini memakan waktu ~30–60 detik. Halaman otomatis reload saat selesai.
                                </div>
                            </div>
                            <script>
                            (function () {
                                var statusUrl  = '{{ route("client.insights.ai-status") }}';
                                var logEl      = document.getElementById('aiLogList');
                                var iconEl     = document.getElementById('aiStatusIcon');
                                var titleEl    = document.getElementById('aiStatusTitle');
                                var lastCount  = 0;
                                var typeIcon   = {
                                    info:     { icon: '›', color: 'var(--text-light)' },
                                    thinking: { icon: '⟳', color: '#6366f1' },
                                    success:  { icon: '✓', color: '#10b981' },
                                    error:    { icon: '✗', color: '#ef4444' },
                                };

                                function renderLogs(logs) {
                                    if (logs.length === lastCount) return;
                                    lastCount = logs.length;
                                    logEl.innerHTML = '';
                                    logs.forEach(function (entry) {
                                        var t   = typeIcon[entry.type] || typeIcon.info;
                                        var row = document.createElement('div');
                                        row.style.cssText = 'display:flex;align-items:flex-start;gap:0.5rem;';
                                        row.innerHTML =
                                            '<span style="color:' + t.color + ';flex-shrink:0;font-size:0.9rem;">' + t.icon + '</span>' +
                                            '<span style="color:' + t.color + ';flex:1;">' + entry.message + '</span>' +
                                            '<span style="color:var(--text-light);font-size:0.72rem;flex-shrink:0;">' + entry.time + '</span>';
                                        logEl.appendChild(row);
                                    });
                                    logEl.scrollTop = logEl.scrollHeight;
                                }

                                function poll() {
                                    fetch(statusUrl)
                                        .then(function (r) { return r.json(); })
                                        .then(function (data) {
                                            renderLogs(data.logs || []);
                                            if (data.ready) {
                                                iconEl.className = 'fas fa-check-circle';
                                                iconEl.style.color = '#10b981';
                                                titleEl.textContent = 'Rekomendasi selesai! Memuat ulang…';
                                                titleEl.style.color = '#10b981';
                                                setTimeout(function () { window.location.reload(); }, 800);
                                            } else {
                                                setTimeout(poll, 2500);
                                            }
                                        })
                                        .catch(function () { setTimeout(poll, 4000); });
                                }

                                poll();
                            })();
                            </script>
                        @endif
                    </div>
                @endif


            </div>
        </section>
    </main>

    <script src="/script.js"></script>
    <script>
    function analysisPoller(accountId, statusUrl) {
        return {
            polling: false,
            logs: [],
            timer: null,
            init() {
                // Auto-start polling if there are logs already (analysis was triggered recently)
                fetch(statusUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (data.logs && data.logs.length > 0 && !data.ready) {
                            this.logs = data.logs;
                            this.startPolling();
                        } else if (data.logs && data.logs.length > 0 && data.ready) {
                            // Analysis just finished — reload to show results
                            window.location.reload();
                        }
                    })
                    .catch(() => {});

                // Listen for analyze form submit on this account card
                const card = document.getElementById('analysis-panel-' + accountId);
                if (card) {
                    const forms = card.closest('[style*="border"]')?.querySelectorAll('form');
                    forms && forms.forEach(form => {
                        const btn = form.querySelector('button[type="submit"]');
                        if (btn && btn.textContent.trim().includes('Analisis')) {
                            form.addEventListener('submit', () => {
                                this.logs = [];
                                this.startPolling();
                            });
                        }
                    });
                }
            },
            startPolling() {
                this.polling = true;
                this.poll();
            },
            poll() {
                if (this.timer) clearTimeout(this.timer);
                fetch(statusUrl)
                    .then(r => r.json())
                    .then(data => {
                        if (data.logs) this.logs = data.logs;
                        if (data.ready) {
                            this.polling = false;
                            setTimeout(() => window.location.reload(), 1200);
                            return;
                        }
                        if (this.polling) {
                            this.timer = setTimeout(() => this.poll(), 2500);
                        }
                    })
                    .catch(() => {
                        if (this.polling) {
                            this.timer = setTimeout(() => this.poll(), 3000);
                        }
                    });
            }
        };
    }
    </script>
</body>
</html>
