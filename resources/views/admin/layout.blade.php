<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - ParaBite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f0f2f5; overflow-x: hidden; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #1a1d29 0%, #2d1b69 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 24px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }
        .sidebar-brand .brand-text {
            color: white;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: -0.5px;
        }
        .sidebar-brand .brand-text small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            opacity: 0.6;
            letter-spacing: 0.5px;
        }
        .sidebar-nav { padding: 16px 12px; }
        .sidebar-nav .nav-label {
            color: rgba(255,255,255,0.35);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 12px 8px;
        }
        .sidebar-nav .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 2px;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }
        .sidebar-nav .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }
        .sidebar-nav .nav-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102,126,234,0.4);
        }
        .sidebar-nav .nav-item i { font-size: 18px; width: 24px; text-align: center; }
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        .topbar {
            background: white;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .topbar .page-title { font-size: 20px; font-weight: 700; color: #1a1d29; margin: 0; }
        .topbar .page-title small { font-size: 13px; font-weight: 400; color: #6b7280; display: block; }
        .topbar .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .topbar .admin-profile .avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        .topbar .admin-profile .info { line-height: 1.3; }
        .topbar .admin-profile .info .name { font-size: 14px; font-weight: 600; color: #1a1d29; }
        .topbar .admin-profile .info .role { font-size: 12px; color: #6b7280; }
        .content-wrapper { padding: 28px 32px; }
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }
        .stat-card .stat-label { font-size: 13px; font-weight: 500; opacity: 0.85; margin-bottom: 4px; }
        .stat-card .stat-value { font-size: 32px; font-weight: 800; letter-spacing: -1px; }
        .stat-card .stat-footer { font-size: 12px; opacity: 0.7; margin-top: 8px; }
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
        }
        .card-modern:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .card-modern .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 24px;
            font-weight: 600;
            font-size: 16px;
            color: #1a1d29;
        }
        .card-modern .card-body { padding: 24px; }
        .table-modern { margin-bottom: 0; }
        .table-modern thead th {
            background: #f8f9fc;
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 2px solid #e9ecef;
        }
        .table-modern tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            color: #374151;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        .table-modern tbody tr:hover { background: #f8f9fc; }
        .badge-modern {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-modern {
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-modern:hover { transform: translateY(-1px); }
        .alert-modern {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
        }
        .form-modern .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-modern .form-control, .form-modern .form-select {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .form-modern .form-control:focus, .form-modern .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }
        .pagination-modern .page-link {
            border: none;
            border-radius: 8px;
            margin: 0 2px;
            color: #374151;
            font-weight: 500;
            padding: 8px 14px;
        }
        .pagination-modern .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 10px rgba(102,126,234,0.3);
        }
        .pagination-modern .page-item.disabled .page-link { color: #d1d5db; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .content-wrapper { padding: 20px 16px; }
            .topbar { padding: 12px 16px; }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo-parabite.png') }}" alt="ParaBite Logo" style="height: 40px; width: auto;">
            <div class="brand-text">
                ParaBite
                <small>Admin Panel</small>
            </div>
        </div>
        <div class="sidebar-nav">
            <div class="nav-label">Main Menu</div>
            <a href="/admin/dashboard" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="/admin/users" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Users
            </a>
            <a href="/admin/locations" class="nav-item {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i> Locations
            </a>
            <div class="nav-label" style="margin-top:16px;">Account</div>
            <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" method="POST" action="/admin/logout" style="display:none;">@csrf</form>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div>
                <div class="page-title">
                    @yield('title', 'Dashboard')
                    <small>@yield('subtitle', '')</small>
                </div>
            </div>
            <div class="admin-profile">
                <div class="info text-end d-none d-md-block">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="role">Administrator</div>
                </div>
                <div class="avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
            </div>
        </div>

        <div class="content-wrapper">
            @if (session('success'))
                <div class="alert alert-success alert-modern alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-modern alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-circle-fill fs-5"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
