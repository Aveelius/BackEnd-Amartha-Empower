<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quick Loan - Amartha Empower</title>
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
                <a class="secondary-button link-button top-nav-button" href="/user/quick-loan">Quick Loan</a>
                <a class="ghost-button link-button top-nav-button" href="/user/payment">Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="/user/ruang-belajar">Ruang Belajar</a>
                <a class="ghost-button link-button top-nav-button" href="/user/komunitas">Komunitas</a>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Quick Loan</span>
                <h1>Ajukan Pinjaman Cepat</h1>
                <p>Isi nominal, tenor, dan dokumen pendukung untuk mengirim pengajuan pinjaman usaha.</p>
            </div>
            <div class="feature-page-nav">
                <a class="ghost-button link-button" href="/user">Kembali ke Dashboard</a>
            </div>
        </header>

        <main class="content-grid">
            <section class="panel panel-full">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Form Pengajuan</span>
                        <h2>Data Pinjaman</h2>
                    </div>
                    <span class="status-pill" id="auth-status">Belum login</span>
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
        </main>

    </div>
</body>
</html>
