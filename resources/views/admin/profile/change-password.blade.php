@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 col-md-3">
            <!-- Sidebar Navigation -->
            <div class="list-group mb-4">
                <a href="{{ route('admin.profile.show') }}" target="_self" class="list-group-item list-group-item-action" style="border-color: rgba(185,28,28,0.1); color: #7f1d1d; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(185,28,28,0.08)'" onmouseout="this.style.backgroundColor='transparent'">
                    Profile Information
                </a>
                <a href="{{ route('admin.profile.edit') }}" target="_self" class="list-group-item list-group-item-action" style="border-color: rgba(185,28,28,0.1); color: #7f1d1d; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(185,28,28,0.08)'" onmouseout="this.style.backgroundColor='transparent'">
                    Edit Profile
                </a>
                <a href="{{ route('admin.profile.change-password') }}" target="_self" class="list-group-item list-group-item-action active" aria-current="true" style="background: linear-gradient(135deg, rgba(185,28,28,0.12), rgba(239,68,68,0.08)); border-color: rgba(185,28,28,0.2); color: #7f1d1d;">
                    <strong>Change Password</strong>
                </a>
                <a href="{{ url('/admin/panel') }}" target="_self" class="list-group-item list-group-item-action" style="border-color: rgba(185,28,28,0.1); color: #b91c1c; font-weight: 600; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(185,28,28,0.08)'" onmouseout="this.style.backgroundColor='transparent'">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="col-12 col-md-9">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info mb-4" role="alert">
                        <strong>Password Requirements:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Minimum 12 characters long</li>
                            <li>At least one uppercase letter (A-Z)</li>
                            <li>At least one lowercase letter (a-z)</li>
                            <li>At least one number (0-9)</li>
                            <li>At least one special character (@$!%*?&)</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('admin.profile.update-password') }}" id="changePasswordForm" onsubmit="return validateForm(event)">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="position-relative">
                                <input 
                                    type="password" 
                                    class="form-control @error('current_password') is-invalid @enderror" 
                                    id="current_password" 
                                    name="current_password" 
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your current admin password"
                                >
                                <small id="currentPasswordStatus" class="d-block mt-2"></small>
                            </div>
                            @error('current_password')
                                <div class="alert alert-danger mt-2 mb-0">
                                    <strong>Error:</strong> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input 
                                type="password" 
                                class="form-control @error('new_password') is-invalid @enderror" 
                                id="new_password" 
                                name="new_password" 
                                required
                                autocomplete="new-password"
                                placeholder="At least 12 characters with mixed case, numbers & symbols"
                            >
                            <div id="passwordStrength" class="mt-2">
                                <small class="form-text" id="strengthText"></small>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            <div id="requirementsList" class="mt-2 small"></div>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input 
                                type="password" 
                                class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                id="new_password_confirmation" 
                                name="new_password_confirmation" 
                                required
                                autocomplete="new-password"
                            >
                            <div id="matchText" class="mt-2"></div>
                            @error('new_password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                Change Password
                            </button>
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning mt-4" role="alert">
                <strong>Note:</strong> After changing your password, you will need to log in again with your new credentials for security purposes.
            </div>
        </div>
    </div>
</div>

<script>
function validateForm(event) {
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    
    // Check current password
    if (!currentPassword.value) {
        event.preventDefault();
        alert('Please enter your current password');
        return false;
    }
    
    const requirements = {
        length: newPassword.value.length >= 12,
        lowercase: /[a-z]/.test(newPassword.value),
        uppercase: /[A-Z]/.test(newPassword.value),
        number: /\d/.test(newPassword.value),
        special: /[@$!%*?&]/.test(newPassword.value),
        allowedChars: /^[a-zA-Z\d@$!%*?&]+$/.test(newPassword.value)
    };
    
    const allMet = Object.values(requirements).every(r => r);
    const passwordsMatch = newPassword.value === confirmPassword.value;
    
    if (!allMet || !passwordsMatch) {
        event.preventDefault();
        let errors = [];
        
        if (!requirements.length) errors.push('Password must be at least 12 characters long');
        if (!requirements.lowercase) errors.push('Password must contain at least one lowercase letter (a-z)');
        if (!requirements.uppercase) errors.push('Password must contain at least one uppercase letter (A-Z)');
        if (!requirements.number) errors.push('Password must contain at least one number (0-9)');
        if (!requirements.special) errors.push('Password must contain at least one special character: @$!%*?&');
        if (!requirements.allowedChars) errors.push('Password can only contain letters, numbers, and these special characters: @$!%*?&');
        if (!passwordsMatch) errors.push('Passwords do not match');
        
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const matchText = document.getElementById('matchText');
    const currentPasswordStatus = document.getElementById('currentPasswordStatus');
    const submitBtn = document.getElementById('submitBtn');

    function checkPasswordStrength(password) {
        let strength = 0;
        const requirements = {
            length: password.length >= 12,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /\d/.test(password),
            special: /[@$!%*?&]/.test(password),
            allowedChars: /^[a-zA-Z\d@$!%*?&]+$/.test(password)
        };

        // Count only the 5 main requirements (not allowedChars, which is enforcing charset)
        Object.keys(requirements).filter(k => k !== 'allowedChars').forEach(req => {
            if (requirements[req]) strength++;
        });

        return { strength, requirements };
    }

    function updateCurrentPasswordStatus() {
        if (!currentPassword.value) {
            currentPasswordStatus.textContent = '';
            return;
        }
        
        currentPasswordStatus.innerHTML = '<span class="text-info">✓ Password entered (will be verified on submit)</span>';
    }

    function updateButtonState() {
        if (!newPassword.value || !confirmPassword.value || !currentPassword.value) {
            submitBtn.disabled = true;
            return;
        }

        const { requirements } = checkPasswordStrength(newPassword.value);
        const allMet = Object.values(requirements).every(r => r);
        const passwordsMatch = newPassword.value === confirmPassword.value;

        if (allMet && passwordsMatch) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    function updatePasswordFeedback() {
        if (!newPassword.value) {
            strengthBar.style.width = '0%';
            strengthText.textContent = '';
            strengthBar.className = 'progress-bar';
            document.getElementById('requirementsList').innerHTML = '';
            submitBtn.disabled = true;
            return;
        }

        const { strength, requirements } = checkPasswordStrength(newPassword.value);

        // Update strength bar
        const width = (strength / 5) * 100;
        strengthBar.style.width = width + '%';

        if (strength < 3) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.innerHTML = '<span class="text-danger">Weak password</span>';
        } else if (strength < 5) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.innerHTML = '<span class="text-warning">Good password</span>';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.innerHTML = '<span class="text-success">Strong password</span>';
        }

        // Show requirements checklist
        const requirementsList = document.getElementById('requirementsList');
        let listHTML = '<div style="padding: 8px; background: rgba(185,28,28,0.03); border-radius: 4px;">';
        
        const reqDescriptions = [
            { key: 'length', label: 'At least 12 characters' },
            { key: 'lowercase', label: 'At least one lowercase letter (a-z)' },
            { key: 'uppercase', label: 'At least one uppercase letter (A-Z)' },
            { key: 'number', label: 'At least one number (0-9)' },
            { key: 'special', label: 'Special character: @$!%*?&' },
            { key: 'allowedChars', label: 'Only letters, numbers & @$!%*?&' }
        ];

        reqDescriptions.forEach(req => {
            const isMet = requirements[req.key];
            const icon = isMet ? '✓' : '✗';
            const color = isMet ? 'text-success' : 'text-danger';
            listHTML += `<div class="form-text ${color}"><small>${icon} ${req.label}</small></div>`;
        });
        
        listHTML += '</div>';
        requirementsList.innerHTML = listHTML;

        // Check if all requirements are met (including allowed characters)
        const allMet = Object.values(requirements).every(r => r);
        
        if (allMet) {
            strengthText.innerHTML = '<span class="text-success">✓ Strong password - meets all requirements</span>';
        }

        updateButtonState();
    }

    function updateMatchFeedback() {
        if (!confirmPassword.value) {
            matchText.textContent = '';
            submitBtn.disabled = true;
            return;
        }

        if (newPassword.value === confirmPassword.value) {
            matchText.innerHTML = '<small class="text-success">✓ Passwords match</small>';
        } else {
            matchText.innerHTML = '<small class="text-danger">✗ Passwords do not match</small>';
        }

        updateButtonState();
    }

    currentPassword.addEventListener('input', () => {
        updateCurrentPasswordStatus();
        updateButtonState();
    });

    newPassword.addEventListener('input', () => {
        updatePasswordFeedback();
        updateMatchFeedback();
    });

    confirmPassword.addEventListener('input', updateMatchFeedback);
});
</script>
@endsection
