<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amartha Empower</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="page-shell">
        <header class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Prototype Connected</span>
                <h1>Amartha Empower</h1>
                <p>
                    Aplikasi pembiayaan, pembelajaran, komunitas, dan pelaporan OJK yang disusun mengikuti alur pada prototype:
                    pengajuan cepat, verifikasi admin, ruang belajar, komunitas usaha perempuan, dan monitoring pembayaran.
                </p>
                <div class="hero-actions">
                    <button class="primary-button" data-demo-login="user">Demo User</button>
                    <button class="secondary-button" data-demo-login="admin">Demo Admin</button>
                </div>
                <p class="hint">Akun demo: user 081234567890 / password, admin Admin / Admin1234.</p>
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
                    <div class="cta-stack" id="home-feature-buttons"></div>
                    <div class="mini-card-list" id="home-quick-cards"></div>
                </div>
            </section>
        </header>

        <main class="content-grid">
            <section class="panel auth-panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">API Auth</span>
                        <h2>Registrasi dan Login</h2>
                    </div>
                    <span class="status-pill" id="auth-status">Belum login</span>
                </div>

                <div class="auth-grid">
                    <form id="register-form" class="form-card">
                        <h3>Daftar Pengguna</h3>
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

                    <form id="login-form" class="form-card">
                        <h3>Login</h3>
                        <input name="login" placeholder="Nomor HP / Email" required>
                        <input name="password" type="password" placeholder="Password" required>
                        <button type="submit" class="secondary-button">Login</button>
                        <button type="button" class="ghost-button" id="logout-button">Logout</button>
                    </form>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Quick Loan</span>
                        <h2>Ajukan Pinjaman Cepat</h2>
                    </div>
                </div>

                <form id="loan-form" class="form-card loan-form">
                    <div class="inline-fields">
                        <input name="amount" type="number" min="1000000" max="10000000" step="50000" value="1000000" required>
                        <select name="tenor_months" required>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                            <option value="18">18 Bulan</option>
                            <option value="24">24 Bulan</option>
                        </select>
                    </div>
                    <div class="inline-fields">
                        <label class="file-upload-field">
                            <span>KTP User</span>
                            <input name="document_ktp" type="file" accept="image/*,.pdf" required>
                        </label>
                        <label class="file-upload-field">
                            <span>Foto Usaha</span>
                            <input name="document_usaha" type="file" accept="image/*" required>
                        </label>
                    </div>
                    <button type="submit" class="primary-button">Kirim Pengajuan</button>
                </form>
                <div id="loan-result" class="result-box"></div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Dashboard User</span>
                        <h2>Status Pinjaman dan Pelunasan</h2>
                    </div>
                </div>
                <div id="user-dashboard" class="dashboard-card empty-state">Login sebagai user untuk melihat dashboard.</div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Ruang Belajar</span>
                        <h2>Video Singkat dan Infografis</h2>
                    </div>
                </div>
                <div id="learning-list" class="tile-grid"></div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Komunitas</span>
                        <h2>Komunitas Usaha Perempuan</h2>
                    </div>
                </div>
                <form id="community-form" class="form-card">
                    <div class="inline-fields">
                        <input name="title" placeholder="Judul posting" required>
                        <select name="category" required>
                            <option value="chat">Chat</option>
                            <option value="tip">Tips</option>
                            <option value="event">Event</option>
                        </select>
                    </div>
                    <textarea name="content" placeholder="Bagikan pengalaman, tips usaha, atau jadwal pelatihan." required></textarea>
                    <div class="inline-fields">
                        <input name="event_date" type="date">
                        <input name="event_location" placeholder="Lokasi event (opsional)">
                    </div>
                    <button type="submit" class="secondary-button">Kirim ke Komunitas</button>
                </form>
                <div id="community-list" class="stack-list"></div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Admin Panel</span>
                        <h2>Verifikasi, Monitoring, dan OJK</h2>
                    </div>
                </div>
                <div class="admin-actions">
                    <button class="secondary-button" id="refresh-admin-dashboard">Refresh Admin</button>
                    <button class="primary-button" id="generate-ojk-report">Generate OJK</button>
                </div>
                <div id="admin-dashboard" class="dashboard-card empty-state">Login sebagai admin untuk melihat panel monitoring.</div>
            </section>
        </main>

        <section class="panel system-log-panel">
            <div class="panel-header">
                <div>
                    <span class="section-tag">System Log</span>
                    <h2>Alur Prototype yang Terhubung</h2>
                </div>
            </div>
            <div id="system-log" class="log-list"></div>
        </section>
    </div>
</body>
</html>
