@extends('admin.layout')

@section('title', 'Create User')
@section('subtitle', 'Add a new user to the platform')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-modern">
            <div class="card-header">
                <span><i class="bi bi-person-plus-fill me-2"></i>New User Details</span>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users" class="form-modern">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="08123456789" required>
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min. 6 characters" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">User Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role...</option>
                                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (Buyer)</option>
                                    <option value="merchant" {{ old('role') === 'merchant' ? 'selected' : '' }}>Merchant (Seller)</option>
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle"></i> Admin accounts can only be created via database seeder
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-modern" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;">
                            <i class="bi bi-check-lg me-1"></i> Create User
                        </button>
                        <a href="/admin/users" class="btn btn-modern" style="background:#e5e7eb;color:#374151;">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
