<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — TestCenter</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }

        /* ── Layout ── */
        .admin-wrap { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px;
            flex-shrink: 0;
            background: #1e1b4b;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px 20px 16px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: -.3px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand span { opacity: .5; font-weight: 400; font-size: .8rem; display: block; margin-top: 2px; }
        .sidebar-nav { padding: 12px 8px; flex: 1; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }
        .sidebar-nav a:hover { background: rgba(255,255,255,.08); color: #fff; }
        .sidebar-nav a.active { background: #4f46e5; color: #fff; }
        .sidebar-nav .nav-icon { font-size: 1rem; width: 20px; text-align: center; }
        .sidebar-section { padding: 8px 12px 4px; font-size: .7rem; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,.3); letter-spacing: .08em; margin-top: 8px; }

        /* ── Main ── */
        .main { flex: 1; margin-left: 220px; display: flex; flex-direction: column; min-height: 100vh; }
        .main-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 28px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .main-header h1 { font-size: 1rem; font-weight: 700; color: #1e293b; }
        .header-user { display: flex; align-items: center; gap: 12px; font-size: .875rem; color: #64748b; }
        .header-user strong { color: #1e293b; }
        .btn-logout {
            background: none; border: 1px solid #e2e8f0; padding: 5px 12px; border-radius: 6px;
            font-size: .8rem; cursor: pointer; color: #64748b; transition: background .15s;
        }
        .btn-logout:hover { background: #f1f5f9; }

        /* ── Page content ── */
        .page-content { flex: 1; padding: 28px; }

        /* ── Alerts ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: .875rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

        /* ── Cards ── */
        .card { background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04); }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header h2 { font-size: .95rem; font-weight: 700; }
        .card-body { padding: 20px; }

        /* ── Tables ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        th { text-align: left; padding: 10px 14px; font-weight: 600; color: #64748b; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: .8rem; text-transform: uppercase; letter-spacing: .04em; }
        td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafbfc; }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 99px; font-size: .75rem; font-weight: 600; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-gray   { background: #f1f5f9; color: #64748b; }
        .badge-indigo { background: #eef2ff; color: #4338ca; }
        .badge-amber  { background: #fffbeb; color: #92400e; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: .875rem; font-weight: 600; cursor: pointer; text-decoration: none; border: none; transition: background .15s, transform .1s; }
        .btn:active { transform: scale(.98); }
        .btn-primary  { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }
        .btn-success  { background: #10b981; color: #fff; }
        .btn-success:hover { background: #059669; }
        .btn-danger   { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-outline  { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        .btn-outline:hover { background: #f9fafb; }
        .btn-sm { padding: 5px 10px; font-size: .8rem; }
        .btn-icon { padding: 6px 8px; font-size: .85rem; }

        /* ── Forms ── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: .85rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-hint  { font-size: .78rem; color: #94a3b8; margin-top: 4px; }
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: .9rem;
            color: #1e293b;
            outline: none;
            transition: border-color .2s;
            background: #fff;
        }
        .form-textarea { resize: vertical; min-height: 90px; }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.1);
        }
        .form-input.is-invalid, .form-textarea.is-invalid, .form-select.is-invalid { border-color: #ef4444; }
        .invalid-feedback { color: #ef4444; font-size: .8rem; margin-top: 4px; }
        .form-check { display: flex; align-items: center; gap: 8px; }
        .form-check input { width: 16px; height: 16px; cursor: pointer; }
        .form-check label { font-size: .875rem; cursor: pointer; margin: 0; }

        /* ── Utility ── */
        .flex { display: flex; }
        .flex-wrap { flex-wrap: wrap; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-4 { margin-top: 16px; }
        .text-sm { font-size: .875rem; }
        .text-xs { font-size: .78rem; }
        .text-muted { color: #94a3b8; }
        .text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 320px; }
        .w-full { width: 100%; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; }

        /* ── Stats card ── */
        .stat-card { background: #fff; border-radius: 10px; padding: 20px 22px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .stat-card .stat-label { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 6px; }
        .stat-card .stat-value { font-size: 2rem; font-weight: 800; color: #1e293b; }
        .stat-card .stat-sub { font-size: .8rem; color: #94a3b8; margin-top: 2px; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 4px; margin-top: 20px; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: .875rem; text-decoration: none; border: 1px solid #e2e8f0; color: #374151; }
        .pagination a:hover { background: #f8fafc; }
        .pagination .active span { background: #4f46e5; color: #fff; border-color: #4f46e5; }

        /* ── Dynamic form rows ── */
        .dyn-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .dyn-row .form-input { flex: 1; }
        .dyn-row .key-input { width: 80px; flex-shrink: 0; }
        .arrow-sep { color: #94a3b8; font-size: .9rem; flex-shrink: 0; }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-wrap">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            TC Admin
            <span>TestCenter</span>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <div class="sidebar-section">Quản lý</div>
            <a href="{{ route('admin.exams.index') }}" class="{{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                <span class="nav-icon">📝</span> Đề thi
            </a>
            <a href="{{ route('admin.questions.index') }}" class="{{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                <span class="nav-icon">❓</span> Câu hỏi
            </a>
        </nav>
    </aside>

    {{-- Main --}}
    <div class="main">
        <header class="main-header">
            <h1>@yield('title', 'Dashboard')</h1>
            <div class="header-user">
                <strong>{{ auth()->user()->name }}</strong>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">Đăng xuất</button>
                </form>
            </div>
        </header>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">✕ {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <div>
                        @foreach($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
