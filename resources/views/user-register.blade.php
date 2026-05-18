<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register User - Amartha Empower</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="page-shell">
        <nav class="top-user-nav" aria-label="Navigasi utama user">
            <a class="top-brand" href="/user">Amartha Empower</a>
            <div class="top-nav-actions">
                <a class="ghost-button link-button top-nav-button" href="/user">Dashboard</a>
                <a class="ghost-button link-button top-nav-button" href="/user/login">Login</a>
                <a class="secondary-button link-button top-nav-button" href="/user/register">Register</a>
                <a class="ghost-button link-button top-nav-button" href="/user/quick-loan">Quick Loan</a>
                <a class="ghost-button link-button top-nav-button" href="/user/payment">Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="/user/ruang-belajar">Ruang Belajar</a>
                <a class="ghost-button link-button top-nav-button" href="/user/komunitas">Komunitas</a>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Register User</span>
                <h1>Buat Akun Baru</h1>
                <p>Daftarkan profil usaha agar bisa mengakses Quick Loan, Ruang Belajar, dan Komunitas.</p>
            </div>
        </header>

        <main class="content-grid auth-page-grid">
            <section class="panel auth-page-panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Pendaftaran</span>
                        <h2>Register</h2>
                    </div>
                    <span class="status-pill" id="auth-status">Belum login</span>
                </div>
                <form id="register-form" class="form-card" data-redirect-after-auth="/user">
                    <input name="name" placeholder="Nama lengkap" required>
                    <input name="phone" placeholder="Nomor HP" required>
                    <input name="email" placeholder="Email opsional">
                    <input name="business_name" placeholder="Nama usaha" required>
                    <input name="domicile" placeholder="Domisili" required>
                    <select name="gender" required>
                        <option value="female">Perempuan</option>
                        <option value="male">Laki-laki</option>
                    </select>
                    <input name="password" type="password" placeholder="Password" required>
                    <button type="submit" class="primary-button">Register</button>
                </form>
                <div class="auth-switch-card">
                    <p>Sudah punya akun?</p>
                    <a class="secondary-button link-button" href="/user/login">Masuk ke Login</a>
                </div>
            </section>
        </main>

        <section class="panel system-log-panel">
            <div class="panel-header">
                <div>
                    <span class="section-tag">System Log</span>
                    <h2>Aktivitas Register</h2>
                </div>
            </div>
            <div id="system-log" class="log-list"></div>
        </section>
    </div>
</body>
</html>
