<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CampusPro ERP</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>?v=<?= time() ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --dark: #0f172a;
            --white: #ffffff;
            --bg-light: #f8fafc;
            --error: #ef4444;
            --glass: rgba(255, 255, 255, 0.8);
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--dark);
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* Left Side: Image Content */
        .login-image-side {
            flex: 1.2;
            position: relative;
            background: url("<?= base_url('images/login-side.png') ?>") no-repeat center center;
            background-size: cover;
            display: none; /* Hidden on mobile */
        }

        @media (min-width: 1024px) {
            .login-image-side {
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                padding: 4rem;
            }
        }

        .login-image-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.2) 50%, transparent 100%);
        }

        .image-content {
            position: relative;
            z-index: 1;
            color: white;
            animation: slideUp 1s ease-out;
        }

        .image-content h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .image-content p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 450px;
            line-height: 1.6;
        }

        /* Right Side: Form Content */
        .login-form-side {
            flex: 1;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            animation: fadeIn 0.8s ease-out;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: var(--primary);
            border-radius: 14px;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
            transform: rotate(-3deg);
        }

        .brand-logo:hover {
            transform: rotate(0deg) scale(1.1);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .subtitle {
            color: var(--secondary);
            margin-bottom: 2.5rem;
            font-size: 1rem;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.6rem;
            color: var(--dark);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
            font-size: 1rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.8rem;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            outline: none;
            background: var(--bg-light);
        }

        .form-control:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        #togglePassword {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary);
            padding: 0.2rem;
        }

        #togglePassword:hover {
            color: var(--primary);
        }

        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.4rem;
            font-weight: 500;
            display: none;
            padding-left: 0.5rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s ease-in-out;
        }

        footer {
            position: absolute;
            bottom: 2rem;
            text-align: center;
            color: var(--secondary);
            font-size: 0.85rem;
            width: 100%;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Mobile specific adjustments */
        @media (max-width: 1023px) {
            .login-form-side {
                flex: 1;
            }
            .login-card {
                max-width: 400px;
            }
        }
    </style>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('js/form-validations.js') ?>"></script>
</head>
<body class="auth-page">
    <div class="login-wrapper">
        <!-- Left Side -->
        <div class="login-image-side">
            <div class="image-content">
                <h2>Excellence in Digital Campus Management</h2>
                <p>Empowering educational institutions with seamless administrative workflows and real-time insights.</p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="login-form-side">
            <div class="login-card">
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Welcome Back</h1>
                <p class="subtitle">Please sign in to your CampusPro account.</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form id="loginForm" action="<?= base_url('login/process') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope field-icon"></i>
                            <input type="email" name="email" id="email" class="form-control" placeholder="name@college.edu" maxlength="50" required>
                        </div>
                        <div id="emailError" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" maxlength="15" required>
                            <span id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div id="passError" class="error-message"></div>
                    </div>

                    <div style="margin-bottom: 2rem; text-align: right;">
                        <a href="<?= base_url('forgot-password') ?>" style="color: var(--primary); font-size: 0.9rem; text-decoration: none; font-weight: 500;">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-submit">
                        Sign In
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <footer>
                    <p>© 2026 CampusPro Enterprise. All rights reserved.</p>
                </footer>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const validator = new FormValidator();

            // Lock field constraints (physically prevent typing)
            validator.lockEmailField('#email');
            validator.lockPasswordField('#password');
            validator.initPasswordToggle('#password', '#togglePassword');

            // Real-time validation
            $('#email').on('input blur', function() {
                const val = $(this).val();
                if (val.length > 0) {
                    validator.validateEmail(val, '#emailError');
                } else {
                    $('#emailError').hide();
                }
            });

            // Focus interactions (Premium feel)
            $('.form-control').on('focus', function() {
                $(this).closest('.form-group').find('.field-icon').css('color', 'var(--primary)');
            }).on('blur', function() {
                $(this).closest('.form-group').find('.field-icon').css('color', 'var(--secondary)');
            });

            // Form submission
            $('#loginForm').on('submit', function(e) {
                const isEmailValid = validator.validateEmail($('#email').val(), '#emailError');
                const btn = $('.btn-submit');
                
                if (!isEmailValid) {
                    e.preventDefault();
                    return;
                }

                // Add loading state
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Signing in...');
            });
        });
    </script>
</body>
</html>

