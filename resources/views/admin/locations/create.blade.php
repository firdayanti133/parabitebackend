@extends('admin.layout')

@section('title', 'Create Location')
@section('subtitle', 'Add a new location to the platform')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-modern">
            <div class="card-header">
                <span><i class="bi bi-geo-alt-fill me-2"></i>New Location Details</span>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/locations" class="form-modern">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Location Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter location name (e.g., Jakarta, Bandung)" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> Location name must be unique
                        </small>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-modern" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;">
                            <i class="bi bi-check-lg me-1"></i> Create Location
                        </button>
                        <a href="/admin/locations" class="btn btn-modern" style="background:#e5e7eb;color:#374151;">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
