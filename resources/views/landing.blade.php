<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amartha Empower</title>

    <link rel="stylesheet" href="/assets/app.css">
<script type="module" src="/assets/app.js"></script>
    <style>
        .login-page-shell {
            min-height: 100vh;
            width: min(100%, 640px);
            margin: 0 auto;
            padding: 32px 16px;
            display: grid;
            place-items: center;
        }

        .login-card {
            width: min(100%, 460px);
            padding: 32px;
            text-align: center;
            background: var(--surface);
            backdrop-filter: blur(18px);
            border: 1px solid var(--line);
            border-radius: 32px;
            box-shadow: var(--shadow);
        }

        .login-card h1 {
            font-size: clamp(2rem, 8vw, 3rem);
        }

        .login-card p {
            color: var(--muted);
            line-height: 1.7;
        }

        .login-brand {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-brand img {
            width: min(230px, 76%);
            height: auto;
            object-fit: contain;
        }

        .login-card .form-card {
            text-align: left;
        }

        .login-status {
            margin-top: 16px;
        }

        .login-log {
            margin-top: 14px;
            text-align: left;
        }

        .login-log:empty {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-page-shell">
        <main class="login-card" aria-labelledby="login-title">
            <div class="login-brand">
                <img src="/assets/amartha-empower-logo.png" alt="Amartha Empower">
            </div>
            <span class="eyebrow">Amartha Empower</span>
            <h1 id="login-title">Login Akun</h1>
            <p>
                Masuk dengan akun yang sudah terdaftar untuk membuka dashboard user atau admin.
            </p>
            <form id="login-form" class="form-card landing-login-form" data-role-redirect="true" data-user-redirect="/user" data-admin-redirect="/admin">
                <input name="login" placeholder="Nama / Nomor HP / Email" required>
                <input name="password" type="password" placeholder="Password" required>
                <button type="submit" class="primary-button">Login</button>
                <a class="secondary-button link-button" href="/user/register">Register</a>
            </form>
            <p class="hint">Gunakan akun demo user atau admin yang sudah tersedia di database.</p>
            <span class="status-pill login-status" id="auth-status">Belum login</span>
            <div id="system-log" class="log-list login-log" aria-live="polite"></div>
        </main>
    </div>
</body>
</html>
