/**
 * Reusable Form Validation Utility
 * Usage: Import this file in your forms and use the validation functions
 */

class FormValidator {
    
    /**
     * Prevent typing disallowed characters in Name (letters and spaces only)
     * Limit to 20 characters
     */
    lockNameField(elementId) {
        $(elementId).on('input', function(e) {
            let val = $(this).val().replace(/[^a-zA-Z\s]/g, '');
            if (val.length > 20) {
                val = val.substring(0, 20);
            }
            $(this).val(val);
        });
    }

    /**
     * Prevent typing numbers after @ in Email
     * Limit to 35 characters
     */
    lockEmailField(elementId) {
        $(elementId).on('input', function(e) {
            let val = $(this).val();
            const parts = val.split('@');
            if (parts.length > 1) {
                parts[1] = parts[1].replace(/\d/g, '');
                val = parts[0] + '@' + parts[1];
            }
            if (val.length > 35) {
                val = val.substring(0, 35);
            }
            $(this).val(val);
        });
    }

    /**
     * Limit Password to 15 characters
     */
    lockPasswordField(elementId) {
        $(elementId).on('input', function(e) {
            let val = $(this).val();
            if (val.length > 15) {
                $(this).val(val.substring(0, 15));
            }
        });
    }

    /**
     * Setup Password Visibility Toggle
     */
    initPasswordToggle(inputId, toggleId) {
        $(toggleId).css('cursor', 'pointer');
        $(toggleId).on('click', function() {
            const passwordField = $(inputId);
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });
    }

    /**
     * Limit Marks to 100 and block arrow keys
     */
    lockMarksField(elementId) {
        $(elementId).on('keydown', function(e) {
            // Block up (38) and down (40) arrow keys
            if (e.which === 38 || e.which === 40) {
                e.preventDefault();
            }
        });

        $(elementId).on('input', function(e) {
            let val = $(this).val();
            // Remove non-numeric
            val = val.replace(/[^0-9]/g, '');
            // Convert to number
            let num = parseInt(val);
            if (num > 100) {
                $(this).val('100');
            } else {
                $(this).val(val);
            }
        });
    }

    showError(elementId, message) {
        $(elementId).text(message).addClass('visible').css({
            'display': 'block',
            'color': '#ef4444'
        });
    }

    hideError(elementId) {
        $(elementId).removeClass('visible').css('display', 'none');
    }

    validateName(value, errorId = '#nameError') {
        const val = value.trim();
        const nameRegex = /^[a-zA-Z\s]+$/;
        if (val === "") {
            this.showError(errorId, 'Full Name is required.');
            return false;
        }
        else if (!nameRegex.test(val)) {
            this.showError(errorId, 'Name must contain only letters and spaces.');
            return false;
        }
        else if (val.length > 20) {
            this.showError(errorId, 'Name must not exceed 20 characters.');
            return false;
        }
        this.hideError(errorId);
        return true;
    }

    validateEmail(value, errorId = '#emailError') {
        const val = value.trim();
        if (val === "") {
            this.showError(errorId, 'Email is required.');
            return false;
        }
        if (val.length > 35) {
            this.showError(errorId, 'Email must not exceed 35 characters.');
            return false;
        }
        const emailParts = val.split('@');
        if (emailParts.length !== 2) {
            this.showError(errorId, 'Invalid email. Include an @ symbol.');
            return false;
        } 
        if (!/[a-zA-Z]/.test(emailParts[0])) {
            this.showError(errorId, 'Email must contain letters before @.');
            return false;
        } 
        if (/\d/.test(emailParts[1])) {
            this.showError(errorId, 'Numbers are forbidden in the domain part.');
            return false;
        } 
        const domain = emailParts[1].toLowerCase();
        if (domain.includes('gmail') && domain !== 'gmail.com') {
            if (domain.startsWith('gmail.')) {
                const afterDot = domain.slice(6);
                if ('com'.startsWith(afterDot)) {
                    this.hideError(errorId);
                    return true;
                }
            }
            this.showError(errorId, 'Gmail domain must be exactly gmail.com.');
            return false;
        } 
        if (!/^[a-zA-Z.-]+\.[a-zA-Z]{2,}$/.test(emailParts[1])) {
            this.showError(errorId, 'Domain must be valid.');
            return false;
        }
        this.hideError(errorId);
        return true;
    }

    validateMobile(iti, value, errorId = '#mobileError') {
        const val = value.trim();
        if (val === "") {
            this.showError(errorId, 'Mobile number is required.');
            return false;
        }
        if (iti.isValidNumber()) {
            this.hideError(errorId);
            return true;
        } else {
            this.showError(errorId, 'Invalid mobile number format.');
            return false;
        }
    }

    validateDOB(value, errorId = '#dobError', minAge = 18) {
        if (!value) {
            this.showError(errorId, 'Date of birth is required.');
            return false;
        }
        const dob = new Date(value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        if (age < minAge) {
            this.showError(errorId, 'You must be at least ' + minAge + ' years old.');
            return false;
        }
        this.hideError(errorId);
        return true;
    }
}
