<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amartha Empower User</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="page-shell">
        <nav class="top-user-nav" aria-label="Navigasi utama user">
            <a class="top-brand" href="/user" aria-label="Amartha Empower Dashboard">
                <img src="/assets/amartha-empower-logo.png" alt="Amartha Empower">
            </a>
            <div class="top-nav-actions">
                <button class="primary-button top-nav-button" data-demo-login="user">Demo User</button>
                <a class="ghost-button link-button top-nav-button" href="/user/quick-loan">Quick Loan</a>
                <a class="ghost-button link-button top-nav-button" href="/user/payment">Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="/user/ruang-belajar">Ruang Belajar</a>
                <a class="ghost-button link-button top-nav-button" href="/user/komunitas">Komunitas</a>
            </div>
        </nav>

        <header class="hero hero-compact">
            <div class="hero-copy">
                <span class="eyebrow">Portal User</span>
                <h1>Halaman Pengguna</h1>
                <p>Ajukan pinjaman cepat, lihat status pelunasan, selesaikan modul belajar, dan masuk ke komunitas usaha perempuan.</p>
            </div>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Akses User</span>
                        <h2>Status Akun</h2>
                    </div>
                    <span class="status-pill" id="auth-status">Belum login</span>
                </div>
                <div class="mini-card-list">
                    <div class="mini-card">
                        <strong>Masuk ke akun</strong>
                        <p>Gunakan halaman login untuk membuka dashboard, Quick Loan, Ruang Belajar, dan Komunitas.</p>
                        <div class="hero-actions">
                            <button type="button" class="primary-button" id="logout-button">Logout</button>
                        </div>
                    </div>
                </div>
            </section>
        </header>

        <main class="content-grid">
            <section class="panel panel-full user-home-panel" id="dashboard-user">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Beranda User</span>
                        <h2>Dashboard dan Menu Utama</h2>
                    </div>
                </div>
                <div class="home-dashboard-grid">
                    <div class="home-menu-column">
                        <div class="profile-summary-card" id="profile-summary">
                            <div class="profile-avatar" aria-hidden="true">AE</div>
                            <div>
                                <span class="section-tag">Profil</span>
                                <h3>Login untuk melihat profil.</h3>
                                <p class="empty-state">Data profil dan biodata akan tampil setelah pengguna masuk.</p>
                                <div class="hero-actions profile-card-actions">
                                    <a class="ghost-button link-button" href="/user/profile">Edit Profil</a>
                                </div>
                            </div>
                        </div>
                        <div class="nav-button-grid" aria-label="Navigasi fitur user">
                            <a class="feature-nav-button coral" href="/user/quick-loan">
                                <strong>Quick Loan</strong>
                                <span>Ajukan pinjaman cepat</span>
                            </a>
                            <a class="feature-nav-button mint" href="/user/ruang-belajar">
                                <strong>Ruang Belajar</strong>
                                <span>Ikuti modul singkat</span>
                            </a>
                            <a class="feature-nav-button blue" href="/user/komunitas">
                                <strong>Komunitas</strong>
                                <span>Masuk forum usaha</span>
                            </a>
                            <a class="feature-nav-button mint" href="/user/payment">
                                <strong>Pembayaran</strong>
                                <span>Bayar dan unggah bukti</span>
                            </a>
                        </div>
                        <div class="cta-stack" id="home-feature-buttons"></div>
                        <div class="mini-card-list" id="home-quick-cards"></div>
                    </div>
                    <div>
                        <div class="dashboard-card-header">
                            <span class="section-tag">Dashboard User</span>
                            <h3>Status Pinjaman dan Pelunasan</h3>
                        </div>
                        <div id="user-dashboard" class="dashboard-card empty-state">Login sebagai user untuk melihat dashboard.</div>
                    </div>
                </div>
            </section>

        </main>

    </div>
</body>
</html>
