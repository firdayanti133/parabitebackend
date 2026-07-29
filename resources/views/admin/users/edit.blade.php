@extends('admin.layout')

@section('title', 'Edit User')
@section('subtitle', 'Update user information and role')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-modern">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-2"></i>Edit User &mdash; {{ $user->name }}</span>
                <span class="badge badge-modern" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;">
                    ID: #{{ $user->id }}
                </span>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users/{{ $user->id }}" class="form-modern">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required>
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password
                                    <small class="text-muted fw-normal">(leave blank to keep current)</small>
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="New password (optional)">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">User Role</label>
                                @if($user->role === 'admin')
                                    <input type="text" class="form-control" value="Admin (Protected)" disabled>
                                    <input type="hidden" name="role" value="admin">
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-shield-lock-fill"></i> Admin role cannot be changed via this form
                                    </small>
                                @else
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="user" {{ old('role', $user->role_name) === 'user' ? 'selected' : '' }}>User (Buyer)</option>
                                        <option value="merchant" {{ old('role', $user->role_name) === 'merchant' ? 'selected' : '' }}>Merchant (Seller)</option>
                                    </select>
                                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-modern" style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;">
                            <i class="bi bi-check-lg me-1"></i> Update User
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
