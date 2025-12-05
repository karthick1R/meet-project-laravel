@extends('layouts.app')

@section('title', 'Register')

@section('content')

<style>
    body {
        background: linear-gradient(135deg, #eef4ff, #dbe8ff);
        font-family: "Inter", sans-serif;
    }

    .register-wrapper {
        max-width: 450px;
        margin: 40px auto;
    }

    .register-card {
        background: #ffffff;
        padding: 35px;
        border-radius: 18px;
        box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.08);
        animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .register-header {
        text-align: center;
        margin-bottom: 25px;
    }

    .register-header i {
        font-size: 55px;
        color: #3b7df0;
    }

    .register-header h2 {
        font-weight: 600;
        margin-top: 10px;
        color: #333;
    }

    .form-label {
        font-weight: 500;
        color: #333;
    }

    .form-control {
        border-radius: 12px;
        padding: 12px;
        font-size: 15px;
        border: 1px solid #d7d7d7;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: #3b7df0;
        box-shadow: 0 0 0 4px rgba(59, 125, 240, 0.15);
    }

    .btn-register {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: none;
        font-size: 17px;
        font-weight: 600;
        background: linear-gradient(135deg, #3b7df0, #5a8bff);
        color: white;
        transition: 0.3s;
    }

    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0px 8px 20px rgba(59, 125, 240, 0.25);
    }

    .login-link a {
        color: #3b7df0;
        font-weight: 600;
        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
</style>

<div class="register-wrapper">

    <div class="register-card">

        <div class="register-header">
            <i class="fa-solid fa-user-plus"></i>
            <h2>Create an Account</h2>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            @if(isset($product_key) && isset($token))
                <input type="hidden" name="registration_token" value="{{ $token }}">
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Registering with: {{ $product_key->email }}
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input 
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    name="name"
                    value="{{ old('name', isset($product_key) ? '' : '') }}"
                    required
                >
                @error('name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input 
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ old('email', isset($product_key) ? $product_key->email : '') }}"
                    {{ isset($product_key) ? 'readonly' : '' }}
                    required
                >
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input 
                    type="text"
                    class="form-control @error('phone') is-invalid @enderror"
                    name="phone"
                    value="{{ old('phone', isset($product_key) ? $product_key->phone : '') }}"
                    {{ isset($product_key) ? 'readonly' : '' }}
                >
                @error('phone')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Logo (Optional)</label>
                <input 
                    type="file"
                    class="form-control @error('logo') is-invalid @enderror"
                    name="logo"
                    accept="image/*"
                >
                <small class="text-muted">Upload a logo for your profile (max 2MB)</small>
                @error('logo')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input 
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password"
                    required
                >
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input 
                    type="password"
                    class="form-control"
                    name="password_confirmation"
                    required
                >
            </div>

            <button class="btn-register">
                <i class="fa-solid fa-user-plus me-2"></i> Complete Registration
            </button>

        </form>

        <div class="text-center mt-3 login-link">
            <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>

    </div>
</div>

@endsection
