<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Billing | Koneva</title>
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
                    <li><a href="{{ route('superadmin.admins.index') }}">Admin Management</a></li>
                    <li><a href="{{ route('superadmin.audit-logs.index') }}">Audit Logs</a></li>
                    <li><a href="{{ route('superadmin.billing.index') }}" class="active">Billing</a></li>
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
                    <span class="section-tag">Billing</span>
                    <h1>Invoice <span>Management</span></h1>
                    <p>Buat invoice, pantau status pembayaran, dan sinkronkan ke reporting.</p>
                </div>

                @if (session('status'))
                    <div class="auth-status show">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <div class="portal-card" style="margin-bottom: 1rem;">
                    <form method="POST" action="{{ route('superadmin.billing.store') }}" class="contact-form auth-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <select name="client_id" required>
                                    <option value="">Pilih Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->company_name }} ({{ $client->user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="project_id">
                                    <option value="">Pilih Project (opsional)</option>
                                    @foreach ($clients as $client)
                                        @foreach ($client->projects as $project)
                                            <option value="{{ $project->id }}">{{ $client->company_name }} - {{ $project->title }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="text" name="invoice_number" placeholder="Invoice Number (INV-2026-0001)" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="title" placeholder="Invoice Title" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="number" step="0.01" name="amount" placeholder="Amount" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="currency" value="IDR" maxlength="3" placeholder="Currency" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <select name="status" required>
                                    <option value="draft">draft</option>
                                    <option value="issued" selected>issued</option>
                                    <option value="paid">paid</option>
                                    <option value="overdue">overdue</option>
                                    <option value="cancelled">cancelled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="date" name="issued_at" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="date" name="due_date">
                            </div>
                            <div class="form-group">
                                <input type="text" name="notes" placeholder="Notes (optional)">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Invoice</button>
                    </form>
                </div>

                <div class="portal-card" style="overflow-x: auto;">
                    <h3>Billing Records</h3>
                    @if ($billingRecords->isEmpty())
                        <p>No billing records yet.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding: 0.6rem;">Invoice</th>
                                    <th style="text-align:left; padding: 0.6rem;">Client</th>
                                    <th style="text-align:left; padding: 0.6rem;">Project</th>
                                    <th style="text-align:left; padding: 0.6rem;">Amount</th>
                                    <th style="text-align:left; padding: 0.6rem;">Issued</th>
                                    <th style="text-align:left; padding: 0.6rem;">Status</th>
                                    <th style="text-align:left; padding: 0.6rem;">Update</th>
                                    <th style="text-align:left; padding: 0.6rem;">Export</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($billingRecords as $item)
                                    <tr>
                                        <td style="padding: 0.6rem;">{{ $item->invoice_number }}<br><small>{{ $item->title }}</small></td>
                                        <td style="padding: 0.6rem;">{{ $item->client->company_name }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->project?->title ?? '-' }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->currency }} {{ number_format((float) $item->amount, 2) }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->issued_at?->format('Y-m-d') }}</td>
                                        <td style="padding: 0.6rem;">{{ $item->status }}</td>
                                        <td style="padding: 0.6rem;">
                                            <form method="POST" action="{{ route('superadmin.billing.update-status', $item) }}" style="display:flex; gap:0.4rem;">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" style="padding:0.35rem; border-radius:8px; border:1px solid #cbd5e1;">
                                                    <option value="draft" @selected($item->status === 'draft')>draft</option>
                                                    <option value="issued" @selected($item->status === 'issued')>issued</option>
                                                    <option value="paid" @selected($item->status === 'paid')>paid</option>
                                                    <option value="overdue" @selected($item->status === 'overdue')>overdue</option>
                                                    <option value="cancelled" @selected($item->status === 'cancelled')>cancelled</option>
                                                </select>
                                                <button type="submit" class="nav-auth-link">Save</button>
                                            </form>
                                        </td>
                                        <td style="padding: 0.6rem;">
                                            <a href="{{ route('superadmin.billing.pdf', $item) }}" class="nav-auth-link">PDF</a>
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
