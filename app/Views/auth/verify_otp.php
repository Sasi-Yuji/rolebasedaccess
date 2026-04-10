<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | CampusPro ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2563eb; --primary-dark: #1d4ed8; --secondary: #64748b; --dark: #0f172a; --white: #ffffff; --bg-light: #f8fafc; --error: #ef4444; --border: #e2e8f0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: var(--dark); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 440px; padding: 2.5rem; background: white; border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .brand-logo { width: 56px; height: 56px; background: var(--primary); border-radius: 14px; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 2rem; transform: rotate(-3deg); margin-left: auto; margin-right: auto; }
        h1 { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 700; text-align: center; margin-bottom: 0.5rem; }
        .subtitle { color: var(--secondary); text-align: center; margin-bottom: 2rem; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; text-align: center; }
        .otp-container { display: flex; justify-content: center; gap: 0.75rem; }
        .otp-input { width: 100%; text-align: center; padding: 1rem; border: 1.5px solid var(--border); border-radius: 12px; font-size: 1.5rem; font-weight: 700; outline: none; background: var(--bg-light); letter-spacing: 0.5rem; }
        .otp-input:focus { border-color: var(--primary); background: white; }
        .btn-submit { width: 100%; padding: 0.9rem; background: var(--primary); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; margin-top: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn-submit:hover { background: var(--primary-dark); }
        .alert { padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.85rem; text-align: center; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .resend { display: block; text-align: center; margin-top: 1.5rem; color: var(--secondary); text-decoration: none; font-size: 0.85rem; }
        .resend b { color: var(--primary); cursor: pointer; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="brand-logo"><i class="fas fa-shield-halved"></i></div>
        <h1>Verify OTP</h1>
        <p class="subtitle">Please enter the 6-digit code sent to <b><?= session()->get('reset_email') ?></b></p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('verify-otp') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <input type="text" name="otp" class="otp-input" placeholder="000000" maxlength="6" pattern="\d{6}" required autofocus autocomplete="one-time-code">
            </div>
            <button type="submit" class="btn-submit">Verify Identity <i class="fas fa-check"></i></button>
        </form>
        
        <p class="resend">Code expires in 5 minutes. Didn't receive? <a href="<?= base_url('forgot-password') ?>"><b>Resend</b></a></p>
    </div>
</body>
</html>
