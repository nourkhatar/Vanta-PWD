@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="glass-card auth-card">
        <div class="text-center mb-4">
            <h2 style="color: var(--primary-accent); margin-bottom: 5px;">Vanta PWD</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">ACCESS CONTROL</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label">USERNAME</label>
                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus autocomplete="username">
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">PASSWORD</label>
                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    INITIATE SESSION
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('register') }}" style="color: var(--secondary-accent); font-size: 0.9rem; text-decoration: none;">
                    CREATE NEW IDENTITY
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
