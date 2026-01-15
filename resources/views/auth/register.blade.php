@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="glass-card auth-card">
        <div class="text-center mb-4">
            <h2 style="color: var(--primary-accent); margin-bottom: 5px;">Vanta PWD</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">NEW IDENTITY</p>
        </div>

        <form method="POST" action="{{ route('register') }}" id="register-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">EMAIL</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                @error('email')
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
                <label for="user_pin" class="form-label">SECURITY PIN (4 digits)</label>
                <input id="user_pin" type="password" class="form-control" name="user_pin" inputmode="numeric" maxlength="4" required>
                @error('user_pin')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="user_pin_confirmation" class="form-label">CONFIRM SECURITY PIN</label>
                <input id="user_pin_confirmation" type="password" class="form-control" name="user_pin_confirmation" inputmode="numeric" maxlength="4" required>
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('register-form');
        const pinInput = document.getElementById('user_pin');
        const pinConfirmInput = document.getElementById('user_pin_confirmation');

        function sanitizePinInput(e) {
            const cleaned = e.target.value.replace(/\D/g, '').slice(0, 4);
            if (e.target.value !== cleaned) {
                e.target.value = cleaned;
            }
        }

        pinInput.addEventListener('input', sanitizePinInput);
        pinConfirmInput.addEventListener('input', sanitizePinInput);

        form.addEventListener('submit', function (e) {
            const pin = pinInput.value.trim();
            const pinConfirm = pinConfirmInput.value.trim();

            const isNumeric = /^\d{4}$/.test(pin);

            if (!isNumeric) {
                e.preventDefault();
                alert('PIN must be exactly 4 numeric digits.');
                return;
            }

            if (pin !== pinConfirm) {
                e.preventDefault();
                alert('PIN and confirmation PIN must match.');
            }
        });
    });
</script>
@endpush
@endsection
