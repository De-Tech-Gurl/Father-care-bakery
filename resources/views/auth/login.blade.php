@extends('layouts.customer')

@section('title', 'Login - Father Care Bakery')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card">
                    <img src="{{ asset('images/logo.png') }}" alt="Father Care Bakery" class="auth-logo">
                    <h1 class="fw-700 mb-2">Welcome back</h1>
                    <p class="text-muted mb-4">Sign in to manage your orders and checkout faster.</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-600">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                                autofocus
                                autocomplete="username"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-600">Password</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                                autocomplete="current-password"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input id="remember" type="checkbox" class="form-check-input" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">Login</button>
                    </form>

                    <p class="text-center text-muted mt-4 mb-0">
                        New here?
                        <a href="{{ route('register') }}" class="text-primary-custom fw-600">Create an account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
