<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amartha Empower</title>

    <link rel="stylesheet" href="/assets/app.css">
<script type="module" src="/assets/app.js"></script>
</head>
<body>
    <div class="page-shell">
        <header class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Amartha Empower</span>
                <h1>Login Akun</h1>
                <p>
                    Masuk dengan akun yang sudah terdaftar. Jika akun user benar, sistem akan membuka tampilan user. Jika akun admin benar, sistem akan membuka tampilan admin.
                </p>
                <form id="login-form" class="form-card landing-login-form" data-role-redirect="true" data-user-redirect="/user" data-admin-redirect="/admin">
                    <input name="login" placeholder="Nama / Nomor HP / Email" required>
                    <input name="password" type="password" placeholder="Password" required>
                    <button type="submit" class="primary-button">Login</button>
                </form>
                <p class="hint">Gunakan akun demo user atau admin yang sudah tersedia di database.</p>
            </div>

            <section class="phone-mock">
                <div class="phone-screen">
                    <div class="brand-row">
                        <div class="brand-mark">A</div>
                        <div>
                            <strong>Amartha Empower</strong>
                            <p>Pinjaman, belajar, komunitas</p>
                        </div>
                    </div>
                    <div class="cta-stack">
                        <div class="feature-card coral"><strong>User</strong><p>Dashboard, quick loan, pembayaran, ruang belajar, komunitas.</p></div>
                        <div class="feature-card mint"><strong>Admin</strong><p>Verifikasi pinjaman, pembayaran, komunitas, dan laporan OJK.</p></div>
                    </div>
                    <div class="mini-card-list">
                        <div class="mini-card"><strong>Demo User</strong><p>081234567890 / password</p></div>
                        <div class="mini-card"><strong>Demo Admin</strong><p>Admin / Admin1234</p></div>
                    </div>
                </div>
            </section>
        </header>

        <section class="panel system-log-panel">
            <div class="panel-header">
                <div>
                    <span class="section-tag">Login</span>
                    <h2>Status Akses</h2>
                </div>
                <span class="status-pill" id="auth-status">Belum login</span>
            </div>
            <div id="system-log" class="log-list"></div>
        </section>
    </div>
</body>
</html>
