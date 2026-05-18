<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengajuan Loan User - Amartha Empower</title>
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
                <a class="ghost-button link-button top-nav-button" href="/admin">Dashboard Admin</a>
                <a class="secondary-button link-button top-nav-button" href="/admin/loans">Pengajuan Loan</a>
                <a class="ghost-button link-button top-nav-button" href="/admin/payments">Verifikasi Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="/user">Ke User</a>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Pengajuan Loan</span>
                <h1>Daftar Pengajuan User</h1>
                <p>Admin dapat melihat seluruh pengajuan pinjaman user, memeriksa dokumen yang dikirim, lalu menyetujui atau menolak pengajuan.</p>
            </div>
            <span class="status-pill" id="auth-status">Belum login</span>
        </header>

        <main class="content-grid">
            <section class="panel panel-full">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Loan User</span>
                        <h2>Seluruh Pengajuan</h2>
                    </div>
                    <button class="secondary-button" id="refresh-admin-loans">Refresh Pengajuan</button>
                </div>
                <div id="admin-loans-list" class="stack-list empty-state">Login sebagai admin untuk melihat pengajuan loan user.</div>
            </section>
        </main>

        <section class="panel system-log-panel">
            <div class="panel-header">
                <div>
                    <span class="section-tag">System Log</span>
                    <h2>Aktivitas Pengajuan</h2>
                </div>
            </div>
            <div id="system-log" class="log-list"></div>
        </section>
    </div>
</body>
</html>
