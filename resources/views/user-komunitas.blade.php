<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Komunitas - Amartha Empower</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="page-shell">
        <nav class="top-user-nav" aria-label="Navigasi utama user">
            <a class="top-brand" href="/user">Amartha Empower</a>
            <div class="top-nav-actions">
                <button class="primary-button top-nav-button" data-demo-login="user">Demo User</button>
                <a class="ghost-button link-button top-nav-button" href="/user/quick-loan">Quick Loan</a>
                <a class="ghost-button link-button top-nav-button" href="/user/payment">Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="/user/ruang-belajar">Ruang Belajar</a>
                <a class="secondary-button link-button top-nav-button" href="/user/komunitas">Komunitas</a>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Komunitas</span>
                <h1>Komunitas Usaha Perempuan</h1>
                <p>Bagikan pengalaman, tips usaha, dan jadwal pelatihan bersama pengguna lain.</p>
            </div>
            <div class="feature-page-nav">
                <a class="ghost-button link-button" href="/user">Kembali ke Dashboard</a>
            </div>
        </header>

        <main class="content-grid">
            <section class="panel panel-full">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Forum Komunitas</span>
                        <h2>Posting dan Diskusi</h2>
                    </div>
                    <span class="status-pill" id="auth-status">Belum login</span>
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
                    <input name="event_location" placeholder="Lokasi event (opsional)">
                    <button type="submit" class="secondary-button">Kirim ke Komunitas</button>
                </form>
                <div id="community-list" class="stack-list feature-list"></div>
            </section>
        </main>

    </div>
</body>
</html>
