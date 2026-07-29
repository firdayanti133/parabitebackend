@extends('admin.layout')

@section('title', 'Manage Locations')
@section('subtitle', 'View, create, edit and manage all locations')

@section('content')
<div class="card card-modern">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-geo-alt-fill me-2"></i>All Locations</span>
        <a href="/admin/locations/create" class="btn btn-modern" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;">
            <i class="bi bi-plus-lg me-1"></i> Add Location
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Location Name</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locations as $location)
                        <tr>
                            <td><span class="fw-semibold">#{{ $location->id }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:32px;height:32px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;font-size:12px;">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <span class="fw-medium">{{ $location->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $location->created_at->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                <a href="/admin/locations/{{ $location->id }}/edit" class="btn btn-sm btn-modern" style="background:#f59e0b;color:white;">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </a>
                                <form method="POST" action="/admin/locations/{{ $location->id }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this location? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-modern" style="background:#ef4444;color:white;">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <span class="fw-medium">No locations found</span>
                                <p class="mb-0 mt-1" style="font-size:13px;">Get started by adding your first location.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($locations->hasPages())
        <div class="card-footer bg-transparent border-top-0 pt-0">
            <div class="d-flex justify-content-center">
                {{ $locations->links('pagination::bootstrap-4') }}
            </div>
        </div>
    @endif
</div>
@endsection
