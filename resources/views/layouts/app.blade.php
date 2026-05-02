<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Course Equivalency') — IT Faculty</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:   #1a3c5e;
            --secondary: #2c6fad;
            --accent:    #e8a820;
            --sidebar-w: 260px;
        }

        * { font-family: 'IBM Plex Sans', sans-serif; }

        body {
            background: #f0f2f5;
            min-height: 100vh;
        }

        /* ── Sidebar ──────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--primary);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.4;
        }

        .sidebar-brand small {
            color: rgba(255,255,255,0.5);
            font-size: 0.72rem;
        }

        .sidebar-nav { padding: 1rem 0; flex: 1; }

        .sidebar-nav .nav-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(255,255,255,0.35);
            padding: 0.5rem 1.25rem 0.25rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.6rem 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-radius: 0;
            transition: all 0.18s;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
            border-left: 3px solid var(--accent);
            padding-left: calc(1.25rem - 3px);
        }

        .sidebar-nav .nav-link i { font-size: 1rem; }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer .user-name {
            color: #fff;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .sidebar-footer .user-role {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.5);
            text-transform: capitalize;
        }

        /* ── Main content ─────────────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 900;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar h6 {
            margin: 0;
            font-weight: 600;
            color: var(--primary);
            font-size: 1rem;
        }

        .page-body { padding: 1.75rem; }

        /* ── Status badges ────────────────────────────────────── */
        .badge-status-new      { background: #6c757d !important; }
        .badge-status-review   { background: #0d6efd !important; }
        .badge-status-ready    { background: #fd7e14 !important; }
        .badge-status-entered  { background: #6f42c1 !important; }
        .badge-status-approved { background: #198754 !important; }
        .badge-status-rejected { background: #dc3545 !important; }

        .badge { font-size: 0.72rem; font-weight: 500; letter-spacing: 0.03em; }

        /* ── Cards ────────────────────────────────────────────── */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: var(--primary);
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.25rem;
        }

        /* ── Tables ───────────────────────────────────────────── */
        .table th {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            background: #f8fafc;
        }

        .table td { vertical-align: middle; font-size: 0.875rem; }

        /* ── Buttons ──────────────────────────────────────────── */
        .btn-primary { background: var(--secondary); border-color: var(--secondary); }
        .btn-primary:hover { background: var(--primary); border-color: var(--primary); }

        /* ── Form controls ────────────────────────────────────── */
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(44,111,173,0.15);
        }

        /* ── Status timeline ──────────────────────────────────── */
        .timeline { position: relative; padding-left: 1.5rem; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 8px; top: 0; bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }
        .timeline-item { position: relative; margin-bottom: 1.25rem; }
        .timeline-dot {
            position: absolute;
            left: -1.5rem;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--secondary);
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px var(--secondary);
        }

        /* ── Auth page ────────────────────────────────────────── */
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            overflow: hidden;
        }

        .auth-logo {
            background: var(--primary);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }

        code, .mono { font-family: 'IBM Plex Mono', monospace; }
    </style>

    @stack('styles')
</head>
<body>

@auth
<div class="sidebar">
    <div class="sidebar-brand">
        <h5><i class="bi bi-mortarboard-fill me-2" style="color: var(--accent)"></i>IT Faculty</h5>
        <small>Course Equivalency System</small>
    </div>

    <nav class="sidebar-nav">
        @if(auth()->user()->isAdmin())
            <div class="nav-label">Admin Panel</div>
            <a href="{{ route('admin.requests.index') }}"
               class="nav-link {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> All Requests
            </a>
            <a href="{{ route('admin.requests.create') }}"
               class="nav-link {{ request()->routeIs('admin.requests.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> New Request
            </a>
        @else
            <div class="nav-label">Academic Panel</div>
            <a href="{{ route('academic.requests.index') }}"
               class="nav-link {{ request()->routeIs('academic.requests.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> Pending Approval
            </a>
        @endif

        <div class="nav-label mt-3">General</div>
        <a href="{{ route('requests.public.create') }}" class="nav-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Public Form
        </a>
        <a href="{{ route('requests.track') }}" class="nav-link" target="_blank">
            <i class="bi bi-search"></i> Request Tracker
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                 style="width:34px;height:34px;flex-shrink:0">
                <i class="bi bi-person text-white" style="font-size:1rem"></i>
            </div>
            <div class="overflow-hidden">
                <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-sm w-100 text-white"
                    style="background:rgba(255,255,255,0.1);border:none;font-size:0.8rem">
                <i class="bi bi-box-arrow-right me-1"></i> Sign out
            </button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <h6>@yield('page-title', 'Dashboard')</h6>
        <div class="d-flex align-items-center gap-3">
            @if(session('success'))
                <span class="badge bg-success py-2 px-3">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                </span>
            @endif
            @if(session('error'))
                <span class="badge bg-danger py-2 px-3">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                </span>
            @endif
        </div>
    </div>

    <div class="page-body">
        @yield('content')
    </div>
</div>

@else
    {{-- Unauthenticated: full-width --}}
    @yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
