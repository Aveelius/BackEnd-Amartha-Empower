<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran - Amartha Empower</title>
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
                <a class="secondary-button link-button top-nav-button" href="/user/payment">Pembayaran</a>
                <a class="ghost-button link-button top-nav-button" href="/user/ruang-belajar">Ruang Belajar</a>
                <a class="ghost-button link-button top-nav-button" href="/user/komunitas">Komunitas</a>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Pembayaran</span>
                <h1>Bayar Cicilan</h1>
                <p>Pilih metode pembayaran, masukkan nominal, lalu unggah bukti pembayaran untuk diverifikasi admin.</p>
            </div>
            <span class="status-pill" id="auth-status">Belum login</span>
        </header>

        <main class="content-grid">
            <section class="panel panel-full">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Metode Pembayaran</span>
                        <h2>Bank atau E-wallet</h2>
                    </div>
                </div>
                <form id="payment-form" class="form-card">
                    <div class="payment-method-grid">
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="bank" checked>
                            <strong>Bank Transfer</strong>
                            <span>Pilih bank tujuan transfer yang tersedia.</span>
                        </label>
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="ewallet">
                            <strong>E-wallet</strong>
                            <span>DANA/OVO/GoPay 081234567890</span>
                        </label>
                    </div>
                    <div class="payment-provider-panel" id="bank-provider-panel">
                        <label class="file-upload-field">
                            <span>Pilih Bank</span>
                            <select name="payment_provider" id="payment-provider-select" required>
                                <option value="BCA">BCA - 1234567890 a.n. Amartha Empower</option>
                                <option value="BRI">BRI - 1122334455 a.n. Amartha Empower</option>
                                <option value="BNI">BNI - 6677889900 a.n. Amartha Empower</option>
                                <option value="Mandiri">Mandiri - 9988776655 a.n. Amartha Empower</option>
                                <option value="BSI">BSI - 5566778899 a.n. Amartha Empower</option>
                            </select>
                        </label>
                    </div>
                    <div class="inline-fields">
                        <input name="amount" type="number" min="1000" step="1000" placeholder="Nominal pembayaran" required>
                        <label class="file-upload-field">
                            <span>Bukti Pembayaran</span>
                            <input name="proof_payment" type="file" accept="image/*,.pdf" required>
                        </label>
                    </div>
                    <button type="submit" class="primary-button">Kirim Pembayaran</button>
                </form>
                <div id="payment-result" class="result-box"></div>
            </section>
        </main>

        <section class="panel system-log-panel">
            <div class="panel-header">
                <div>
                    <span class="section-tag">System Log</span>
                    <h2>Aktivitas Pembayaran</h2>
                </div>
            </div>
            <div id="system-log" class="log-list"></div>
        </section>
    </div>
</body>
</html>
