<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Deliverables | Koneva</title>
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
                    <li><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                    <li><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                    <li><a href="{{ route('admin.requests.index') }}">Requests</a></li>
                    <li><a href="{{ route('admin.deliverables.index') }}" class="active">Deliverables</a></li>
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
                    <span class="section-tag">Deliverable Publishing</span>
                    <h1>Admin <span>Deliverables</span></h1>
                    <p>Upload files, manage versions, and control client visibility.</p>
                </div>

                @if (session('status'))
                    <div class="auth-status show">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="auth-status show error">{{ $errors->first() }}</div>
                @endif

                <div class="portal-card" style="margin-bottom:1rem;">
                    <form method="POST" action="{{ route('admin.deliverables.store') }}" enctype="multipart/form-data" class="contact-form auth-form">
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
                            <div class="form-group"><input type="text" name="title" placeholder="Deliverable Title" required></div>
                            <div class="form-group">
                                <select name="status" required>
                                    <option value="draft">draft</option>
                                    <option value="review">review</option>
                                    <option value="published">published</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea name="description" rows="3" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group" style="flex-direction:row; align-items:center; gap:0.6rem;">
                            <input type="checkbox" name="is_visible_to_client" value="1" style="width:auto;">
                            <label>Visible to client (only effective if status is published)</label>
                        </div>
                        <div class="form-group">
                            <input type="file" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Deliverable</button>
                    </form>
                </div>

                <div class="portal-card" style="overflow-x:auto;">
                    @if ($deliverables->isEmpty())
                        <p>No deliverables yet.</p>
                    @else
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:0.6rem;">Title</th>
                                    <th style="text-align:left; padding:0.6rem;">Client</th>
                                    <th style="text-align:left; padding:0.6rem;">Version</th>
                                    <th style="text-align:left; padding:0.6rem;">File</th>
                                    <th style="text-align:left; padding:0.6rem;">State</th>
                                    <th style="text-align:left; padding:0.6rem;">Update</th>
                                    <th style="text-align:left; padding:0.6rem;">File Ops</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliverables as $item)
                                    <tr>
                                        <td style="padding:0.6rem;">{{ $item->title }}<br><small>{{ $item->project?->title ?? '-' }}</small></td>
                                        <td style="padding:0.6rem;">{{ $item->client->company_name }}</td>
                                        <td style="padding:0.6rem;">v{{ $item->version }}</td>
                                        <td style="padding:0.6rem;">
                                            @if ($item->file_url)
                                                <a href="{{ $item->file_url }}" target="_blank" rel="noopener noreferrer" class="nav-auth-link">Open</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="padding:0.6rem;">{{ $item->status }} / {{ $item->is_visible_to_client ? 'visible' : 'hidden' }}</td>
                                        <td style="padding:0.6rem;">
                                            <form method="POST" action="{{ route('admin.deliverables.update', $item) }}" class="contact-form" style="gap:0.4rem;">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-group" style="margin:0;">
                                                    <select name="status" style="padding:0.35rem; border-radius:8px; border:1px solid #cbd5e1;">
                                                        <option value="draft" @selected($item->status === 'draft')>draft</option>
                                                        <option value="review" @selected($item->status === 'review')>review</option>
                                                        <option value="published" @selected($item->status === 'published')>published</option>
                                                    </select>
                                                </div>
                                                <div class="form-group" style="margin:0; flex-direction:row; align-items:center; gap:0.4rem;">
                                                    <input type="checkbox" name="is_visible_to_client" value="1" @checked($item->is_visible_to_client) style="width:auto;">
                                                    <label>Visible</label>
                                                </div>
                                                <div class="form-group" style="margin:0;">
                                                    <input type="text" name="description" value="{{ $item->description }}" placeholder="Description">
                                                </div>
                                                <button type="submit" class="nav-auth-link">Save</button>
                                            </form>
                                        </td>
                                        <td style="padding:0.6rem;">
                                            <form method="POST" action="{{ route('admin.deliverables.replace-file', $item) }}" enctype="multipart/form-data" style="display:flex; gap:0.4rem; margin-bottom:0.4rem; align-items:center;">
                                                @csrf
                                                <input type="file" name="file" required style="max-width:200px;">
                                                <button type="submit" class="nav-auth-link">Replace</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.deliverables.destroy', $item) }}" onsubmit="return confirm('Delete this deliverable and its file permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="nav-auth-link">Delete</button>
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
