<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Pengguna - Amartha Empower</title>
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
                <a class="ghost-button link-button top-nav-button" href="/user/komunitas">Komunitas</a>
                <a class="secondary-button link-button top-nav-button" href="/user/profile">Profil</a>
            </div>
        </nav>

        <header class="feature-page-header">
            <div>
                <span class="eyebrow">Profil Pengguna</span>
                <h1>Edit Profil</h1>
                <p>Perbarui foto profil, biodata, dan data usaha yang dipakai di portal Amartha Empower.</p>
            </div>
            <div class="feature-page-nav">
                <span class="status-pill" id="auth-status">Belum login</span>
                <a class="ghost-button link-button" href="/user">Kembali ke Dashboard</a>
            </div>
        </header>

        <main class="content-grid">
            <section class="panel panel-full" id="profil-pengguna">
                <div class="panel-header">
                    <div>
                        <span class="section-tag">Data Profil</span>
                        <h2>Foto Profil dan Biodata</h2>
                    </div>
                </div>
                <div class="profile-editor-grid">
                    <div class="profile-preview-card" id="profile-preview">
                        <div class="profile-avatar profile-avatar-large" aria-hidden="true">AE</div>
                        <div>
                            <h3>Profil belum tersedia</h3>
                            <p class="empty-state">Login sebagai user untuk mengedit foto profil dan biodata.</p>
                        </div>
                    </div>
                    <form class="form-card" id="profile-form">
                        <div class="inline-fields">
                            <input name="name" placeholder="Nama lengkap" required>
                            <input name="phone" placeholder="Nomor HP" required>
                        </div>
                        <input name="email" type="email" placeholder="Email">
                        <div class="inline-fields">
                            <input name="business_name" placeholder="Nama usaha" required>
                            <input name="domicile" placeholder="Domisili" required>
                        </div>
                        <select name="gender" required>
                            <option value="">Pilih gender</option>
                            <option value="female">Perempuan</option>
                            <option value="male">Laki-laki</option>
                        </select>
                        <textarea name="bio" placeholder="Tulis biodata singkat, pengalaman usaha, atau kebutuhan pendampingan"></textarea>
                        <label class="file-upload-field">
                            <span>Foto profil</span>
                            <input name="profile_photo" type="file" accept="image/png,image/jpeg,image/webp">
                        </label>
                        <button class="secondary-button" type="submit">Simpan Profil</button>
                        <div id="profile-result" class="result-box empty-state">Login sebagai user untuk mengubah profil.</div>
                    </form>
                </div>
            </section>
        </main>

    </div>
</body>
</html>
