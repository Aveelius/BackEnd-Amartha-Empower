<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amartha Empower Admin</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="page-shell">
        <nav class="top-user-nav" aria-label="Navigasi admin">
            <a class="top-brand" href="/admin">Amartha Empower Admin</a>
            <div class="top-nav-actions">
                <button class="secondary-button top-nav-button" data-demo-login="admin">Demo Admin</button>
                <a class="secondary-button link-button top-nav-button" href="/admin">Dashboard Admin</a>
                <a class="ghost-button link-button top-nav-button" href="/admin/loans">Pengajuan Loan</a>
                <a class="ghost-button link-button top-nav-button" href="/admin/payments">Verifikasi Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="#admin-community-panel">Kelola Komunitas</a>
                <a class="ghost-button link-button top-nav-button" href="/">Kembali</a>
                <a class="ghost-button link-button top-nav-button" href="/user">Ke User</a>
            </div>
        </nav>

        <header class="hero hero-compact">
            <div class="hero-copy">
                <span class="eyebrow">Portal Admin</span>
                <h1>Halaman Admin</h1>
                <p>Panel admin dipisahkan agar verifikasi pengajuan, pencairan dana, monitoring pinjaman, dan laporan OJK lebih fokus.</p>
            </div>
        </header>

        <main class="content-grid">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Beranda</span>
                        <h2>Ringkasan Fitur</h2>
                    </div>
                </div>
                <div class="cta-stack" id="home-feature-buttons"></div>
                <div class="mini-card-list" id="home-quick-cards"></div>
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
                    <a class="ghost-button link-button" href="/admin/loans">Semua Pengajuan Loan</a>
                    <a class="ghost-button link-button" href="/admin/payments">Verifikasi Pembayaran</a>
                </div>
                <div id="admin-dashboard" class="dashboard-card empty-state">Login sebagai admin untuk melihat panel monitoring.</div>
            </section>

            <section class="panel panel-full" id="admin-community-panel">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Komunitas</span>
                        <h2>Kelola Posting Komunitas</h2>
                    </div>
                </div>
                <form id="community-form" class="form-card">
                    <div class="inline-fields">
                        <input name="title" placeholder="Judul posting admin" required>
                        <select name="category" required>
                            <option value="chat">Chat</option>
                            <option value="tip">Tips</option>
                            <option value="event">Event</option>
                        </select>
                    </div>
                    <textarea name="content" placeholder="Tulis pengumuman, tips, atau jadwal kegiatan komunitas." required></textarea>
                    <div class="inline-fields">
                        <input name="event_date" type="date">
                        <input name="event_location" placeholder="Lokasi event (opsional)">
                    </div>
                    <button type="submit" class="secondary-button">Posting sebagai Admin</button>
                </form>
                <div id="community-list" class="stack-list feature-list"></div>
            </section>
        </main>

        <section class="panel system-log-panel">
            <div class="panel-header">
                <div>
                    <span class="section-tag">System Log</span>
                    <h2>Aktivitas Admin</h2>
                </div>
            </div>
            <div id="system-log" class="log-list"></div>
        </section>
    </div>
</body>
</html>
