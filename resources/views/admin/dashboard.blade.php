@extends('admin.layout')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of your platform')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-footer"><i class="bi bi-arrow-up"></i> All registered accounts</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                <i class="bi bi-shield-fill"></i>
            </div>
            <div class="stat-label">Admins</div>
            <div class="stat-value">{{ $adminUsers }}</div>
            <div class="stat-footer"><i class="bi bi-shield-check"></i> Platform administrators</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="stat-label">Buyers (User)</div>
            <div class="stat-value">{{ $regularUsers }}</div>
            <div class="stat-footer"><i class="bi bi-cart"></i> Regular customers</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                <i class="bi bi-store-fill"></i>
            </div>
            <div class="stat-label">Sellers (Merchant)</div>
            <div class="stat-value">{{ $merchantUsers }}</div>
            <div class="stat-footer"><i class="bi bi-graph-up"></i> Active merchants</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-modern">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Recent Users</span>
                <a href="/admin/users" class="btn btn-sm btn-modern" style="background:#667eea;color:white;">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentUsers as $user)
                                <tr>
                                    <td><span class="fw-semibold">#{{ $user->id }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm" style="width:32px;height:32px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:12px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @php
                                            $roleColors = [
                                                'admin' => 'background:linear-gradient(135deg,#f093fb,#f5576c);color:white;',
                                                'merchant' => 'background:linear-gradient(135deg,#43e97b,#38f9d7);color:#1a5c2a;',
                                                'user' => 'background:linear-gradient(135deg,#4facfe,#00f2fe);color:#1a3a5c;',
                                            ];
                                            $displayRole = $user->role === 'admin' ? 'admin' : $user->role_name;
                                        @endphp
                                        <span class="badge badge-modern" style="{{ $roleColors[$displayRole] ?? 'background:#e5e7eb;color:#374151;' }}">
                                            {{ ucfirst($displayRole) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $user->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i> No users found yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-modern">
            <div class="card-header">
                <span><i class="bi bi-pie-chart-fill me-2"></i>Role Distribution</span>
            </div>
            <div class="card-body">
                @php
                    $total = max($totalUsers, 1);
                    $adminPct = round(($adminUsers / $total) * 100);
                    $userPct = round(($regularUsers / $total) * 100);
                    $merchantPct = round(($merchantUsers / $total) * 100);
                @endphp
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium" style="font-size:13px;">
                                <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:linear-gradient(135deg,#f093fb,#f5576c);"></span>
                                Admin
                            </span>
                            <span class="fw-bold">{{ $adminPct }}%</span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:6px;background:#f0f0f0;">
                            <div class="progress-bar" style="width:{{ $adminPct }}%;background:linear-gradient(135deg,#f093fb,#f5576c);border-radius:6px;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium" style="font-size:13px;">
                                <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:linear-gradient(135deg,#4facfe,#00f2fe);"></span>
                                Buyer
                            </span>
                            <span class="fw-bold">{{ $userPct }}%</span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:6px;background:#f0f0f0;">
                            <div class="progress-bar" style="width:{{ $userPct }}%;background:linear-gradient(135deg,#4facfe,#00f2fe);border-radius:6px;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium" style="font-size:13px;">
                                <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:linear-gradient(135deg,#43e97b,#38f9d7);"></span>
                                Merchant
                            </span>
                            <span class="fw-bold">{{ $merchantPct }}%</span>
                        </div>
                        <div class="progress" style="height:8px;border-radius:6px;background:#f0f0f0;">
                            <div class="progress-bar" style="width:{{ $merchantPct }}%;background:linear-gradient(135deg,#43e97b,#38f9d7);border-radius:6px;"></div>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="text-center text-muted" style="font-size:13px;">
                    <i class="bi bi-people me-1"></i> {{ $total }} total accounts registered
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
