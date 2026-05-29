<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Pembayaran - Amartha Empower</title>
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
                <a class="ghost-button link-button top-nav-button" href="/admin/loans">Pengajuan Loan</a>
                <a class="secondary-button link-button top-nav-button" href="/admin/payments">Verifikasi Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="/user">Ke User</a>
                <button type="button" class="ghost-button top-nav-button" id="logout-button">Logout</button>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Verifikasi Pembayaran</span>
                <h1>Pembayaran User</h1>
                <p>Periksa nominal, metode pembayaran, dan bukti pembayaran user sebelum memverifikasi atau menolak pembayaran.</p>
            </div>
            <span class="status-pill" id="auth-status">Belum login</span>
        </header>

        <main class="content-grid">
            <section class="panel panel-full">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Pembayaran</span>
                        <h2>Daftar Pembayaran User</h2>
                    </div>
                    <button class="secondary-button" id="refresh-admin-payments">Refresh Pembayaran</button>
                </div>
                <div id="admin-payments-list" class="stack-list empty-state">Login sebagai admin untuk melihat pembayaran user.</div>
            </section>
        </main>

    </div>
</body>
</html>
