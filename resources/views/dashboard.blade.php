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
                    <div class="field-actions" style="display: flex; gap: 6px; align-items: center; margin-left: 8px;">
                        <button class="btn-icon" onclick="copyField('{{ $entry->id }}', 'username')" title="Copy Username">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                        <button class="btn-icon" onclick="openPinModal('{{ $entry->id }}', 'username')" title="Reveal Username">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-field">
                <span class="field-label">Password</span>
                <div class="field-value-container">
                    <span class="field-value" id="password-{{ $entry->id }}">••••••••••••••••</span>
                    <div class="field-actions" style="display: flex; gap: 6px; align-items: center; margin-left: 8px;">
                        <button class="btn-icon" onclick="copyField('{{ $entry->id }}', 'password')" title="Copy Password">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                        <button class="btn-icon" onclick="openPinModal('{{ $entry->id }}', 'password')" title="Reveal Password">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="strength-container">
                <span class="strength-label">Security</span>
                <div class="strength-bars" data-entry-id="{{ $entry->id }}">
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                    <div class="strength-bar"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- PIN Modal -->
<div id="pinModal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title">Confirm PIN</h3>
            <button class="close-modal" onclick="closePinModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="form-group">
            <label for="pin-input" class="form-label">Enter your 4-digit PIN</label>
            <input type="password" id="pin-input" class="form-control" inputmode="numeric" maxlength="4">
            <div id="pin-error" class="text-danger" style="margin-top: 0.5rem; display: none;"></div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="closePinModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="submitPin()">Confirm</button>
        </div>
    </div>
</div>

<!-- Copy Toast -->
<div id="copy-toast" style="position: fixed; bottom: 20px; right: 20px; background: rgba(15,23,42,0.9); color: #fff; padding: 10px 16px; border-radius: 6px; font-size: 0.875rem; display: none; z-index: 9999;">
    Copied to clipboard
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
    let currentEntryId = null;
    let currentField = null;

    function openPinModal(entryId, field) {
        currentEntryId = entryId;
        currentField = field;
        const pinModal = document.getElementById('pinModal');
        const pinInput = document.getElementById('pin-input');
        const pinError = document.getElementById('pin-error');

        pinError.style.display = 'none';
        pinError.textContent = '';
        pinInput.value = '';

        pinModal.style.display = 'flex';
        setTimeout(() => pinModal.classList.add('active'), 10);

        pinInput.focus();
    }

    function closePinModal() {
        const pinModal = document.getElementById('pinModal');
        pinModal.classList.remove('active');
        pinModal.style.display = 'none';
        currentEntryId = null;
        currentField = null;
    }

    async function submitPin() {
        const pinInput = document.getElementById('pin-input');
        const pinError = document.getElementById('pin-error');
        const pin = pinInput.value.trim();

        if (!/^\d{4}$/.test(pin)) {
            pinError.textContent = 'PIN must be exactly 4 digits.';
            pinError.style.display = 'block';
            return;
        }

        if (!currentEntryId || !currentField) {
            pinError.textContent = 'Unexpected error. Please close and try again.';
            pinError.style.display = 'block';
            return;
        }

        try {
            const response = await fetch(`/entries/${currentEntryId}/decrypt`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    field: currentField,
                    pin: pin,
                }),
            });

            const data = await response.json();

            if (!response.ok || data.error) {
                pinError.textContent = data.error || 'Decryption failed. Check your PIN.';
                pinError.style.display = 'block';
                return;
            }

            const element = document.getElementById(`${currentField}-${currentEntryId}`);
            if (data[currentField]) {
                element.textContent = data[currentField];
                element.dataset.value = data[currentField];

                if (currentField === 'password') {
                    updateStrengthBars(currentEntryId, data[currentField]);
                }
            }

            closePinModal();
        } catch (error) {
            pinError.textContent = 'Network error. Please try again.';
            pinError.style.display = 'block';
        }
    }

    function updateStrengthBars(entryId, password) {
        const container = document.querySelector(`.strength-bars[data-entry-id="${entryId}"]`);
        if (!container) return;

        const bars = container.querySelectorAll('.strength-bar');
        bars.forEach(bar => bar.classList.remove('active'));

        let score = 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        for (let i = 0; i < bars.length && i < score; i++) {
            bars[i].classList.add('active');
        }
    }

    async function copyField(entryId, field) {
        const element = document.getElementById(`${field}-${entryId}`);
        const value = element.dataset.value || element.textContent;

        if (!value || value.startsWith('••••')) {
            showCopyToast('Reveal the value before copying.');
            return;
        }

        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(value);
            } else {
                const tempInput = document.createElement('textarea');
                tempInput.value = value;
                tempInput.style.position = 'fixed';
                tempInput.style.opacity = '0';
                document.body.appendChild(tempInput);
                tempInput.focus();
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
            }
            showCopyToast('Copied to clipboard');
        } catch (error) {
            console.error('Copy failed', error);
            showCopyToast('Failed to copy. Please try again.');
        }
    }

    function showCopyToast(message) {
        const toast = document.getElementById('copy-toast');
        toast.textContent = message;
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 2000);
    }
</script>
@endpush
@endsection
