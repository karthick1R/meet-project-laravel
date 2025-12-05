@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<style>
    .profile-card {
        max-width: 600px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 18px;
        padding: 24px 28px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .profile-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e5e7eb;
    }

    .profile-label {
        font-weight: 500;
        color: #374151;
    }

    .profile-input {
        border-radius: 12px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
    }

    .btn-save {
        background: linear-gradient(135deg, #3b7df0, #5a8bff);
        border: none;
        color: #fff;
        font-weight: 600;
        border-radius: 12px;
        padding: 10px 18px;
        width: 100%;
    }
</style>

<div class="profile-card">
    <div class="d-flex align-items-center mb-3">
        <div class="me-3">
            <img
                src="{{ $user->logo ? asset('storage/' . $user->logo) : asset('images/default-logo.png') }}"
                alt="Avatar"
                class="profile-avatar"
                onerror="this.onerror=null; this.src='{{ asset('images/default-logo.png') }}';"
            >
        </div>
        <div>
            <h4 class="mb-0">{{ $user->name }}</h4>
            <small class="text-muted text-uppercase">{{ $user->role }}</small>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="profile-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="form-control profile-input @error('name') is-invalid @enderror">
            @error('name')
            <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label class="profile-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="form-control profile-input @error('email') is-invalid @enderror">
            @error('email')
            <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label class="profile-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="form-control profile-input @error('phone') is-invalid @enderror">
            @error('phone')
            <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label class="profile-label">Profile Logo</label>
            <input type="file" name="logo"
                   class="form-control profile-input @error('logo') is-invalid @enderror">
            <small class="text-muted">Optional. Max 2MB image.</small>
            @error('logo')
            <span class="text-danger small d-block">{{ $message }}</span>
            @enderror
        </div>

        <hr>

        <div class="mb-3">
            <label class="profile-label">New Password</label>
            <input type="password" name="password"
                   class="form-control profile-input @error('password') is-invalid @enderror">
            @error('password')
            <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label class="profile-label">Confirm New Password</label>
            <input type="password" name="password_confirmation"
                   class="form-control profile-input">
        </div>

        <button class="btn-save">
            Save Changes
        </button>
    </form>
</div>

@endsection


