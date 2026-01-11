@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="glass-card auth-card">
        <div class="text-center mb-4">
            <h2 style="color: var(--primary-accent); margin-bottom: 5px;">Vanta PWD</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">NEW IDENTITY</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">FULL NAME</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="username" class="form-label">USERNAME</label>
                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autocomplete="username">
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">MASTER PASSWORD</label>
                <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password">
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">CONFIRM PASSWORD</label>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    ESTABLISH LINK
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" style="color: var(--secondary-accent); font-size: 0.9rem; text-decoration: none;">
                    ALREADY REGISTERED? LOGIN
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
