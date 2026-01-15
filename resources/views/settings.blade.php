@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Settings</h2>
        <p>Manage your profile and preferences</p>
    </div>
</div>

<div class="glass-card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div class="card-title">Edit Profile</div>
    </div>

    @if (session('success'))
        <div class="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" id="profile-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username', auth()->user()->username) }}" required>
            @error('username')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <input type="password" id="password" name="password" class="form-control">
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
        </div>

        <div class="form-actions" style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                Save Changes
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to update your profile information?')) {
            e.preventDefault();
        }
    });
</script>
@endpush
@endsection
