<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ruang Belajar - Amartha Empower</title>
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
                <a class="ghost-button link-button top-nav-button" href="/user">Dashboard</a>
                <a class="ghost-button link-button top-nav-button" href="/user/login">Login</a>
                <a class="ghost-button link-button top-nav-button" href="/user/quick-loan">Quick Loan</a>
                <a class="ghost-button link-button top-nav-button" href="/user/payment">Pembayaran</a>
                <a class="secondary-button link-button top-nav-button" href="/user/ruang-belajar">Ruang Belajar</a>
                <a class="ghost-button link-button top-nav-button" href="/user/komunitas">Komunitas</a>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Ruang Belajar</span>
                <h1>Materi Peminjaman Uang</h1>
                <p>Ikuti modul belajar untuk memahami tujuan pinjaman, bunga, tenor, cicilan, dan cara memakai dana secara bertanggung jawab.</p>
            </div>
        </header>

        <main class="content-grid">
            <section class="panel panel-full">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Materi Belajar</span>
                        <h2>Daftar Modul</h2>
                    </div>
                    <span class="status-pill" id="auth-status">Belum login</span>
                </div>
                <div id="learning-list" class="tile-grid"></div>
            </section>
        </main>

        <section class="panel system-log-panel">
            <div class="panel-header">
                <div>
                    <span class="section-tag">System Log</span>
                    <h2>Aktivitas Belajar</h2>
                </div>
            </div>
            <div id="system-log" class="log-list"></div>
        </section>
    </div>
</body>
</html>
