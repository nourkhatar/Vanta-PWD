@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Secure Dashboard</h2>
        <p>Manage your encrypted credentials</p>
    </div>
    
    <button onclick="toggleModal('addEntryModal')" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add New Entry
    </button>
</div>

@if (session('success'))
    <div class="alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert" style="border-color: var(--danger); color: var(--danger); background: rgba(255, 107, 107, 0.1);">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card-grid">
    @foreach($entries as $entry)
        <div class="glass-card">
            <div class="card-header">
                <div class="card-title">{{ $entry->title }}</div>
                <form action="{{ route('entries.destroy', $entry) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-icon danger" title="Delete Entry">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </form>
            </div>
            
            <div class="card-field">
                <span class="field-label">Username</span>
                <div class="field-value-container">
                    <span class="field-value" id="username-{{ $entry->id }}">••••••••••</span>
                    <button class="btn-icon" onclick="decryptField('{{ $entry->id }}', 'username')" title="Reveal Username">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="card-field">
                <span class="field-label">Password</span>
                <div class="field-value-container">
                    <span class="field-value" id="password-{{ $entry->id }}">••••••••••••••••</span>
                    <button class="btn-icon" onclick="decryptField('{{ $entry->id }}', 'password')" title="Reveal Password">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="strength-container">
                <span class="strength-label">Security</span>
                <div class="strength-bars">
                    <!-- Simulated strength visualization -->
                    <div class="strength-bar active"></div>
                    <div class="strength-bar active"></div>
                    <div class="strength-bar active"></div>
                    <div class="strength-bar active"></div>
                    <div class="strength-bar" style="opacity: 0.3;"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Entry Modal -->
<div id="addEntryModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">New Entry</h3>
            <button class="close-modal" onclick="toggleModal('addEntryModal')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('entries.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="title" class="form-label">Title / Service</label>
                <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. Corporate Email">
            </div>

            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="username@example.com">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px;">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('addEntryModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Entry</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    async function decryptField(entryId, field) {
        const element = document.getElementById(`${field}-${entryId}`);
        if (element.textContent !== '••••••••••••••••' && element.textContent !== '••••••••••') {
            // If already revealed, hide it
            element.textContent = field === 'username' ? '••••••••••' : '••••••••••••••••';
            return;
        }

        try {
            const response = await fetch(`/entries/${entryId}/decrypt?field=${field}`);
            if (!response.ok) throw new Error('Decryption failed');
            
            const data = await response.json();
            if (data[field]) {
                element.textContent = data[field];
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to decrypt data. Please try again.');
        }
    }
</script>
@endpush
@endsection
