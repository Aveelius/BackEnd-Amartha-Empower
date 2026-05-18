import './bootstrap';

const state = {
    token: null,
    user: null,
    logs: [],
};

const AUTH_KEYS = {
    user: 'amartha-empower-auth-user',
    admin: 'amartha-empower-auth-admin',
};

const LEGACY_STORAGE_KEY = 'amartha-empower-auth';

const getExpectedRole = () => {
    if (window.location.pathname.startsWith('/admin')) {
        return 'admin';
    }

    if (window.location.pathname.startsWith('/user')) {
        return 'user';
    }

    return null;
};

const expectedRole = getExpectedRole();

const el = {
    authStatus: document.getElementById('auth-status'),
    homeFeatureButtons: document.getElementById('home-feature-buttons'),
    homeQuickCards: document.getElementById('home-quick-cards'),
    userDashboard: document.getElementById('user-dashboard'),
    learningList: document.getElementById('learning-list'),
    communityList: document.getElementById('community-list'),
    adminDashboard: document.getElementById('admin-dashboard'),
    adminLoansList: document.getElementById('admin-loans-list'),
    adminPaymentsList: document.getElementById('admin-payments-list'),
    systemLog: document.getElementById('system-log'),
    loanResult: document.getElementById('loan-result'),
    paymentResult: document.getElementById('payment-result'),
    profileSummary: document.getElementById('profile-summary'),
    profilePreview: document.getElementById('profile-preview'),
    profileForm: document.getElementById('profile-form'),
    profileResult: document.getElementById('profile-result'),
};

const formatCurrency = (value) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);

const formatDate = (value) => value?.slice(0, 10) || '-';

const escapeHtml = (value = '') => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const formatPaymentMethod = (method) => method === 'ewallet' ? 'E-wallet' : 'Bank Transfer';

const formatPaymentProvider = (provider) => provider ? ` - ${provider}` : '';

const setLoanMessage = (message, isError = false) => {
    if (!el.loanResult) {
        return;
    }

    el.loanResult.textContent = message;
    el.loanResult.style.borderLeft = isError ? '6px solid #ff726d' : '6px solid #35b8aa';
};

const setPaymentMessage = (message, isError = false) => {
    if (!el.paymentResult) {
        return;
    }

    el.paymentResult.textContent = message;
    el.paymentResult.style.borderLeft = isError ? '6px solid #ff726d' : '6px solid #35b8aa';
};

const setProfileMessage = (message, isError = false) => {
    if (!el.profileResult) {
        return;
    }

    el.profileResult.textContent = message;
    el.profileResult.style.borderLeft = isError ? '6px solid #ff726d' : '6px solid #35b8aa';
};

const getAuthKey = (role = state.user?.role || expectedRole || 'user') => AUTH_KEYS[role] || AUTH_KEYS.user;

const persistAuth = () => {
    if (!state.user?.role) {
        return;
    }

    localStorage.setItem(getAuthKey(state.user.role), JSON.stringify({
        token: state.token,
        user: state.user,
    }));
};

const clearPersistedAuth = (role = state.user?.role || expectedRole || 'user') => {
    localStorage.removeItem(getAuthKey(role));
};

const redirectAfterAuth = (form) => {
    const navigateTo = (target) => {
        window.setTimeout(() => {
            window.location.href = target;
        }, 0);
    };

    if (form?.hasAttribute('data-role-redirect')) {
        const roleTarget = state.user?.role === 'admin'
            ? form.dataset.adminRedirect
            : form.dataset.userRedirect;

        if (roleTarget) {
            navigateTo(roleTarget);
            return true;
        }

        return false;
    }

    const target = form?.dataset.redirectAfterAuth;

    if (target) {
        navigateTo(target);
        return true;
    }

    return false;
};

const hydrateAuth = () => {
    const raw = localStorage.getItem(getAuthKey()) || (!expectedRole ? localStorage.getItem(LEGACY_STORAGE_KEY) : null);

    if (!raw) {
        return;
    }

    try {
        const parsed = JSON.parse(raw);
        const parsedUser = parsed.user || null;

        if (expectedRole && parsedUser?.role !== expectedRole) {
            clearPersistedAuth();
            return;
        }

        state.token = parsed.token || null;
        state.user = parsedUser;
    } catch {
        clearPersistedAuth();
    }
};

const acceptAuthenticatedUser = (data) => {
    if (expectedRole && data.user.role !== expectedRole) {
        state.token = null;
        state.user = null;
        clearPersistedAuth();
        throw new Error(expectedRole === 'admin'
            ? 'Gunakan akun admin untuk masuk ke halaman admin.'
            : 'Gunakan akun user untuk masuk ke halaman user.');
    }

    state.token = data.token;
    state.user = data.user;
    persistAuth();
};

const log = (message) => {
    if (!el.systemLog) {
        return;
    }

    state.logs.unshift(`${new Date().toLocaleTimeString('id-ID')} - ${message}`);
    el.systemLog.innerHTML = state.logs.slice(0, 8).map((item) => `<div class="log-item">${item}</div>`).join('');
};

const api = async (url, options = {}) => {
    const isFormData = options.body instanceof FormData;

    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
            ...(!isFormData ? { 'Content-Type': 'application/json' } : {}),
            ...(state.token ? { Authorization: `Bearer ${state.token}` } : {}),
            ...(options.headers || {}),
        },
        ...options,
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const message = data.message || Object.values(data.errors || {}).flat().join(', ') || 'Terjadi kesalahan.';
        throw new Error(message);
    }

    return data;
};

const getDocumentUrl = (document) => document.file_path ? `/storage/${document.file_path}` : null;

const isPdfDocument = (document) => document.file_name?.toLowerCase().endsWith('.pdf');

const renderDocumentPreview = (document, url) => {
    if (!url) {
        return '<span class="empty-state">File lama hanya memiliki nama berkas.</span>';
    }

    if (isPdfDocument(document)) {
        return `<iframe class="document-preview document-preview-pdf" src="${url}" title="${document.document_type} ${document.file_name}"></iframe>`;
    }

    return `<img class="document-preview" src="${url}" alt="${document.document_type} ${document.file_name}">`;
};

const renderLoanDocuments = (documents = []) => {
    if (!documents.length) {
        return '<div class="empty-state">Berkas belum tersedia.</div>';
    }

    return `
        <div class="document-list document-list-detail">
            ${documents.map((document) => {
                const url = getDocumentUrl(document);

                return `
                    <div class="document-card">
                        <strong>${document.document_type}</strong>
                        <span>${document.file_name}</span>
                        <span>Status: ${document.verification_status || 'pending'}</span>
                        ${renderDocumentPreview(document, url)}
                        ${url ? `<a class="ghost-button link-button" href="${url}" target="_blank" rel="noopener">Buka Berkas di Tab Baru</a>` : ''}
                    </div>
                `;
            }).join('')}
        </div>
    `;
};

const renderAdminLoanCard = (item, withDetail = false) => `
    <div class="stack-item">
        <div class="meta-row">
            <strong>${item.application_code}</strong>
            <span>${item.status}</span>
        </div>
        <p>${item.user.name} mengajukan ${formatCurrency(item.amount)} tenor ${item.tenor_months} bulan.</p>
        <div class="meta-row">
            <span>Usaha: ${item.user.business_name || '-'}</span>
            <span>Domisili: ${item.user.domicile || '-'}</span>
            <span>Diajukan: ${formatDate(item.submitted_at || item.created_at)}</span>
        </div>
        ${withDetail ? `
            <details class="loan-detail">
                <summary>Lihat Detail Pengajuan</summary>
                <div class="loan-detail-grid">
                    <div>
                        <strong>Data User</strong>
                        <p>Nama: ${item.user.name}</p>
                        <p>HP: ${item.user.phone || '-'}</p>
                        <p>Email: ${item.user.email || '-'}</p>
                        <p>Usaha: ${item.user.business_name || '-'}</p>
                    </div>
                    <div>
                        <strong>Data Loan</strong>
                        <p>Nominal: ${formatCurrency(item.amount)}</p>
                        <p>Tenor: ${item.tenor_months} bulan</p>
                        <p>Status: ${item.status}</p>
                        <p>Catatan admin: ${item.admin_notes || '-'}</p>
                    </div>
                </div>
                <div class="loan-document-section">
                    <strong>Berkas yang Dikirim User</strong>
                    ${renderLoanDocuments(item.documents)}
                </div>
            </details>
        ` : ''}
        <div class="loan-card-actions">
            <button class="secondary-button" data-approve-loan="${item.id}">Konfirmasi</button>
            <button class="ghost-button" data-reject-loan="${item.id}">Tolak</button>
        </div>
    </div>
`;

const renderPaymentProofPreview = (payment) => {
    const document = {
        file_name: payment.proof_file_name,
        file_path: payment.proof_file_path,
        document_type: 'Bukti Pembayaran',
    };
    const url = getDocumentUrl(document);

    return `
        ${renderDocumentPreview(document, url)}
        ${url ? `<a class="ghost-button link-button" href="${url}" target="_blank" rel="noopener">Buka Bukti di Tab Baru</a>` : ''}
    `;
};

const renderAdminPaymentCard = (payment) => `
    <div class="stack-item">
        <div class="meta-row">
            <strong>${payment.user?.name || 'User'}</strong>
            <span>${payment.status}</span>
        </div>
        <p>${formatCurrency(payment.amount)} untuk pengajuan ${payment.loan?.application_code || '-'}</p>
        <div class="meta-row">
            <span>Metode: ${formatPaymentMethod(payment.payment_method)}${formatPaymentProvider(payment.payment_provider)}</span>
            <span>Dikirim: ${formatDate(payment.created_at)}</span>
            <span>File: ${payment.proof_file_name}</span>
        </div>
        <details class="loan-detail">
            <summary>Lihat Bukti Pembayaran</summary>
            <div class="loan-document-section">
                ${renderPaymentProofPreview(payment)}
            </div>
        </details>
        <div class="loan-card-actions">
            <button class="secondary-button" data-update-payment-status="${payment.id}" data-payment-status="verified">Verifikasi</button>
            <button class="ghost-button" data-update-payment-status="${payment.id}" data-payment-status="rejected">Tolak</button>
        </div>
    </div>
`;

const renderHome = async () => {
    if (!el.homeFeatureButtons || !el.homeQuickCards) {
        return;
    }

    const data = await api('/api/home');
    el.homeFeatureButtons.innerHTML = data.feature_buttons.map((item) => `<div class="feature-card ${item.color}"><strong>${item.label}</strong></div>`).join('');
    el.homeQuickCards.innerHTML = data.quick_cards.map((item) => `<div class="mini-card"><strong>${item.title}</strong><p>${item.description}</p></div>`).join('');
    log('Beranda prototype berhasil dimuat.');
};

const renderUserDashboard = async () => {
    if (!el.userDashboard) {
        return;
    }

    if (!state.token || state.user?.role !== 'user') {
        el.userDashboard.innerHTML = 'Login sebagai user untuk melihat dashboard.';
        return;
    }

    const data = await api('/api/dashboard/user');
    const summary = data.loan_summary;

    el.userDashboard.innerHTML = summary
        ? `
            <div class="metric-grid">
                <div class="metric"><span>Total Pinjaman</span><strong>${formatCurrency(summary.amount)}</strong></div>
                <div class="metric"><span>Status</span><strong>${summary.status}</strong></div>
                <div class="metric"><span>Tenor</span><strong>${summary.tenor_months} bulan</strong></div>
            </div>
            <div class="meta-row">
                <span>Kode aplikasi: ${summary.application_code}</span>
                <span>Jadwal cicilan berikutnya: ${summary.next_due_date || '-'}</span>
                <span>Nominal berikutnya: ${formatCurrency(summary.next_installment_amount)}</span>
            </div>
            <div class="meta-row">
                <span>Total pelunasan: ${formatCurrency(summary.payoff_amount)}</span>
                <span>Terbayar terverifikasi: ${formatCurrency(summary.verified_payment_amount)}</span>
                <span>Menunggu verifikasi: ${formatCurrency(summary.pending_payment_amount)}</span>
                <span>Sisa pelunasan: ${formatCurrency(summary.remaining_amount)}</span>
            </div>
            <div>
                <div class="meta-row"><span>Progres Pelunasan</span><strong>${summary.repayment_progress}%</strong></div>
                <div class="progress-bar"><span style="width:${summary.repayment_progress}%"></span></div>
            </div>
            <div class="stack-list">
                ${data.payment_history.length ? data.payment_history.map((payment) => `
                    <div class="mini-card">
                        <strong>${formatCurrency(payment.amount)}</strong>
                        <p>Metode: ${formatPaymentMethod(payment.payment_method)}${formatPaymentProvider(payment.payment_provider)} | Status: ${payment.status} | ${formatDate(payment.created_at)}</p>
                    </div>
                `).join('') : '<div class="mini-card empty-state">Belum ada riwayat pembayaran.</div>'}
            </div>
            <div class="stack-list">
                ${data.notifications.map((item) => `<div class="stack-item"><strong>${item.title}</strong><p>${item.message}</p></div>`).join('')}
            </div>
        `
        : 'Belum ada pengajuan pinjaman. Buka halaman Quick Loan untuk membuat pengajuan baru.';

    log('Dashboard user diperbarui dari API.');
};

const getInitials = (name = 'AE') => name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('') || 'AE';

const renderProfileAvatar = (user, large = false) => {
    const classes = `profile-avatar${large ? ' profile-avatar-large' : ''}`;

    return user?.profile_photo_url
        ? `<img class="${classes}" src="${user.profile_photo_url}" alt="Foto profil ${escapeHtml(user.name)}">`
        : `<div class="${classes}" aria-hidden="true">${escapeHtml(getInitials(user?.name))}</div>`;
};

const fillProfileForm = () => {
    if (!el.profileForm || !state.user) {
        return;
    }

    ['name', 'phone', 'email', 'business_name', 'domicile', 'gender', 'bio'].forEach((field) => {
        const input = el.profileForm.elements[field];

        if (input) {
            input.value = state.user[field] || '';
        }
    });
};

const renderProfile = () => {
    if (!el.profileSummary && !el.profilePreview && !el.profileForm) {
        return;
    }

    const canEditProfile = state.token && state.user?.role === 'user';

    if (el.profileForm) {
        [...el.profileForm.elements].forEach((field) => {
            if (field.name || field.type === 'submit') {
                field.disabled = !canEditProfile;
            }
        });
    }

    if (!canEditProfile) {
        if (el.profileSummary) {
            el.profileSummary.innerHTML = `
                <div class="profile-avatar" aria-hidden="true">AE</div>
                <div>
                    <span class="section-tag">Profil</span>
                    <h3>Login untuk melihat profil.</h3>
                    <p class="empty-state">Data profil dan biodata akan tampil setelah pengguna masuk.</p>
                    <div class="hero-actions profile-card-actions">
                        <a class="ghost-button link-button" href="/user/profile">Edit Profil</a>
                    </div>
                </div>
            `;
        }

        if (el.profilePreview) {
            el.profilePreview.innerHTML = `
                <div class="profile-avatar profile-avatar-large" aria-hidden="true">AE</div>
                <div>
                    <h3>Profil belum tersedia</h3>
                    <p class="empty-state">Login sebagai user untuk mengedit foto profil dan biodata.</p>
                </div>
            `;
        }

        setProfileMessage('Login sebagai user untuk mengubah profil.', true);
        return;
    }

    const user = state.user;
    const safeBio = escapeHtml(user.bio || 'Belum ada biodata.');
    const profileMarkup = `
        ${renderProfileAvatar(user)}
        <div>
            <span class="section-tag">Profil</span>
            <h3>${escapeHtml(user.name)}</h3>
            <p>${safeBio}</p>
            <div class="meta-row profile-meta">
                <span>${escapeHtml(user.business_name || '-')}</span>
                <span>${escapeHtml(user.domicile || '-')}</span>
            </div>
            <div class="hero-actions profile-card-actions">
                <a class="ghost-button link-button" href="/user/profile">Edit Profil</a>
            </div>
        </div>
    `;

    if (el.profileSummary) {
        el.profileSummary.innerHTML = profileMarkup;
    }

    if (el.profilePreview) {
        el.profilePreview.innerHTML = `
            ${renderProfileAvatar(user, true)}
            <div>
                <h3>${escapeHtml(user.name)}</h3>
                <p>${safeBio}</p>
                <div class="profile-detail-list">
                    <span>HP: ${escapeHtml(user.phone || '-')}</span>
                    <span>Email: ${escapeHtml(user.email || '-')}</span>
                    <span>Usaha: ${escapeHtml(user.business_name || '-')}</span>
                    <span>Domisili: ${escapeHtml(user.domicile || '-')}</span>
                </div>
            </div>
        `;
    }

    fillProfileForm();
    setProfileMessage('Profil siap diedit.', false);
};

const renderLearning = async () => {
    if (!el.learningList) {
        return;
    }

    if (!state.token) {
        el.learningList.innerHTML = '<div class="tile empty-state">Login untuk membuka progres belajar.</div>';
        return;
    }

    const data = await api('/api/learning-modules');
    el.learningList.innerHTML = data.data.map((item) => {
        const progress = item.progress?.[0];
        const completion = progress?.completion_percent || 0;
        const material = item.content
            ? item.content.split('\n').filter(Boolean).map((paragraph) => `<p>${escapeHtml(paragraph)}</p>`).join('')
            : '<p>Materi sedang disiapkan.</p>';

        return `
            <div class="tile">
                <strong>${escapeHtml(item.title)}</strong>
                <p>${escapeHtml(item.summary)}</p>
                <div class="meta-row">
                    <span>${escapeHtml(item.duration_label)}</span>
                    <span>${completion}% selesai</span>
                </div>
                <details class="learning-material">
                    <summary>Baca Materi</summary>
                    <div>${material}</div>
                </details>
                <div class="progress-bar"><span style="width:${completion}%"></span></div>
                <button class="ghost-button" data-complete-module="${item.id}">Tandai Selesai</button>
            </div>
        `;
    }).join('');

    log('Ruang belajar dimuat.');
};

const renderCommunity = async () => {
    if (!el.communityList) {
        return;
    }

    if (!state.token) {
        el.communityList.innerHTML = '<div class="stack-item empty-state">Login untuk masuk ke komunitas.</div>';
        return;
    }

    const data = await api('/api/community-posts');
    el.communityList.innerHTML = data.data.map((item) => `
        <div class="stack-item">
            <div class="meta-row">
                <strong>${escapeHtml(item.title)}</strong>
                <span>${escapeHtml(item.category)}</span>
            </div>
            <p>${escapeHtml(item.content)}</p>
            <div class="meta-row">
                <span>Oleh ${escapeHtml(item.user.name)} (${escapeHtml(item.user.role)})</span>
                <span>${item.event_date || item.created_at.slice(0, 10)}</span>
            </div>
            ${item.event_location ? `<p>Lokasi: ${escapeHtml(item.event_location)}</p>` : ''}
            ${item.comments.length ? `<div class="stack-list">${item.comments.map((comment) => `<div class="mini-card"><strong>${escapeHtml(comment.user.name)}</strong><p>${escapeHtml(comment.content)}</p></div>`).join('')}</div>` : ''}
            ${state.user?.role === 'admin' ? `<button class="ghost-button" data-delete-community-post="${item.id}">Hapus Posting</button>` : ''}
        </div>
    `).join('');

    log('Komunitas usaha perempuan dimuat.');
};

const renderAdminDashboard = async () => {
    if (!el.adminDashboard) {
        return;
    }

    if (!state.token || state.user?.role !== 'admin') {
        el.adminDashboard.innerHTML = 'Login sebagai admin untuk melihat panel monitoring.';
        return;
    }

    const data = await api('/api/dashboard/admin');
    el.adminDashboard.innerHTML = `
        <div class="metric-grid">
            <div class="metric"><span>Pengajuan Pending</span><strong>${data.metrics.pending_loans}</strong></div>
            <div class="metric"><span>Pinjaman Aktif</span><strong>${data.metrics.active_loans}</strong></div>
            <div class="metric"><span>Peminjam Perempuan</span><strong>${data.metrics.female_borrowers}</strong></div>
        </div>
        <div class="stack-list">
            ${data.recent_applications.map((item) => renderAdminLoanCard(item)).join('')}
        </div>
        <div class="stack-item">
            <strong>Laporan OJK Terbaru</strong>
            <p>${data.latest_ojk_report ? `${data.latest_ojk_report.report_date} | Active loans ${data.latest_ojk_report.active_loans} | Outstanding ${formatCurrency(data.latest_ojk_report.total_outstanding)}` : 'Belum ada laporan.'}</p>
        </div>
        <div class="stack-item">
            <div class="meta-row">
                <strong>Verifikasi Pembayaran</strong>
                <span>${data.pending_payments.length} menunggu</span>
            </div>
            <div class="stack-list">
                ${data.pending_payments.length ? data.pending_payments.map(renderAdminPaymentCard).join('') : '<div class="mini-card empty-state">Tidak ada pembayaran yang menunggu verifikasi.</div>'}
            </div>
        </div>
    `;

    log('Dashboard admin berhasil dimuat.');
};

const renderAdminLoans = async () => {
    if (!el.adminLoansList) {
        return;
    }

    if (!state.token || state.user?.role !== 'admin') {
        el.adminLoansList.innerHTML = 'Login sebagai admin untuk melihat pengajuan loan user.';
        return;
    }

    const data = await api('/api/loans');
    el.adminLoansList.innerHTML = data.data.length
        ? data.data.map((item) => renderAdminLoanCard(item, true)).join('')
        : '<div class="stack-item empty-state">Belum ada pengajuan loan user.</div>';

    log('Seluruh pengajuan loan user dimuat.');
};

const renderAdminPayments = async () => {
    if (!el.adminPaymentsList) {
        return;
    }

    if (!state.token || state.user?.role !== 'admin') {
        el.adminPaymentsList.innerHTML = 'Login sebagai admin untuk melihat pembayaran user.';
        return;
    }

    const data = await api('/api/payments');
    el.adminPaymentsList.innerHTML = data.data.length
        ? data.data.map(renderAdminPaymentCard).join('')
        : '<div class="stack-item empty-state">Belum ada pembayaran user.</div>';

    log('Daftar pembayaran user dimuat.');
};

const syncAuthUi = () => {
    if (!el.authStatus) {
        return;
    }

    el.authStatus.textContent = state.user ? `${state.user.name} (${state.user.role})` : 'Belum login';
};

const ensureAuthState = async () => {
    if (!state.token) {
        return;
    }

    try {
        const data = await api('/api/auth/me');
        state.user = data.user;
        persistAuth();
    } catch {
        state.token = null;
        state.user = null;
        clearPersistedAuth();
    }
};

const refreshAll = async () => {
    await ensureAuthState();
    syncAuthUi();
    renderProfile();
    await Promise.allSettled([renderUserDashboard(), renderLearning(), renderCommunity(), renderAdminDashboard(), renderAdminLoans(), renderAdminPayments()]);
};

document.getElementById('register-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const formData = Object.fromEntries(new FormData(form).entries());

    try {
        const data = await api('/api/auth/register', { method: 'POST', body: JSON.stringify(formData) });
        acceptAuthenticatedUser(data);
        log(`Registrasi berhasil untuk ${data.user.name}.`);
        setLoanMessage('Registrasi berhasil. Anda sudah bisa mengajukan pinjaman.', false);
        if (redirectAfterAuth(form)) {
            return;
        }
        await refreshAll();
    } catch (error) {
        log(error.message);
    }
});

document.getElementById('login-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const formData = Object.fromEntries(new FormData(form).entries());

    try {
        const data = await api('/api/auth/login', { method: 'POST', body: JSON.stringify(formData) });
        acceptAuthenticatedUser(data);
        log(`Login berhasil sebagai ${data.user.role}.`);
        setLoanMessage('Login berhasil. Form Quick Loan sudah aktif.', false);
        if (form.hasAttribute('data-role-redirect')) {
            window.location.href = data.user.role === 'admin'
                ? form.dataset.adminRedirect
                : form.dataset.userRedirect;
            return;
        }
        if (redirectAfterAuth(form)) {
            return;
        }
        await refreshAll();
    } catch (error) {
        window.alert('Nama atau username salah');
        log(error.message);
    }
});

document.getElementById('logout-button')?.addEventListener('click', async () => {
    const roleToClear = state.user?.role || expectedRole;

    if (state.token) {
        try {
            await api('/api/auth/logout', { method: 'POST' });
        } catch (error) {
            log(error.message);
        }
    }

    state.token = null;
    state.user = null;
    clearPersistedAuth(roleToClear);
    setLoanMessage('', false);
    log('Sesi login dihapus.');
    await refreshAll();
});

document.querySelectorAll('[data-demo-login]').forEach((button) => {
    button.addEventListener('click', async () => {
        const role = button.dataset.demoLogin;
        const payload = role === 'admin'
            ? { login: 'Admin', password: 'Admin1234' }
            : { login: '081234567890', password: 'password' };

        try {
            const data = await api('/api/auth/login', { method: 'POST', body: JSON.stringify(payload) });
            acceptAuthenticatedUser(data);
            log(`Demo ${role} aktif.`);
            setLoanMessage(`Demo ${role} aktif. ${role === 'user' ? 'Quick Loan siap dipakai.' : 'Silakan gunakan panel admin.'}`, false);
            await refreshAll();
        } catch (error) {
            log(error.message);
        }
    });
});

document.getElementById('loan-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (state.user?.role !== 'user') {
        setLoanMessage('Quick Loan hanya bisa dipakai setelah login sebagai user.', true);
        log('Login sebagai user untuk mengajukan pinjaman.');
        return;
    }

    const payload = new FormData(event.currentTarget);

    try {
        const data = await api('/api/loans', { method: 'POST', body: payload });
        setLoanMessage(`Pengajuan ${data.data.application_code} terkirim dengan status ${data.data.status}.`, false);
        log('Pengajuan pinjaman cepat berhasil dikirim.');
        await refreshAll();
    } catch (error) {
        setLoanMessage(error.message, true);
        log(error.message);
    }
});

document.getElementById('payment-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (state.user?.role !== 'user') {
        setPaymentMessage('Login sebagai user untuk mengirim pembayaran.', true);
        log('Login sebagai user untuk mengirim pembayaran.');
        return;
    }

    try {
        const data = await api('/api/payments', {
            method: 'POST',
            body: new FormData(event.currentTarget),
        });
        event.currentTarget.reset();
        setPaymentMessage(data.message, false);
        log('Pembayaran dikirim dan menunggu verifikasi admin.');
        await renderUserDashboard();
    } catch (error) {
        setPaymentMessage(error.message, true);
        log(error.message);
    }
});

document.getElementById('profile-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;

    if (state.user?.role !== 'user') {
        setProfileMessage('Login sebagai user untuk mengubah profil.', true);
        log('Login sebagai user untuk mengubah profil.');
        return;
    }

    try {
        const data = await api('/api/auth/profile', {
            method: 'POST',
            body: new FormData(form),
        });
        state.user = data.user;
        persistAuth();
        const photoInput = form.elements.namedItem('profile_photo');

        if (photoInput) {
            photoInput.value = '';
        }

        renderProfile();
        syncAuthUi();
        setProfileMessage(data.message, false);
        log('Profil pengguna berhasil diperbarui.');
    } catch (error) {
        setProfileMessage(error.message, true);
        log(error.message);
    }
});

document.querySelectorAll('input[name="payment_method"]').forEach((input) => {
    input.addEventListener('change', () => {
        const bankPanel = document.getElementById('bank-provider-panel');
        const bankSelect = document.getElementById('payment-provider-select');
        const isBank = input.value === 'bank' && input.checked;

        if (!bankPanel || !bankSelect) {
            return;
        }

        bankPanel.hidden = !isBank;
        bankSelect.required = isBank;
        bankSelect.disabled = !isBank;
    });
});

document.getElementById('community-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!state.token) {
        log('Login terlebih dahulu untuk posting ke komunitas.');
        return;
    }

    const payload = Object.fromEntries(new FormData(event.currentTarget).entries());

    try {
        await api('/api/community-posts', { method: 'POST', body: JSON.stringify(payload) });
        event.currentTarget.reset();
        log('Posting komunitas berhasil dibuat.');
        await renderCommunity();
    } catch (error) {
        log(error.message);
    }
});

document.getElementById('refresh-admin-dashboard')?.addEventListener('click', async () => {
    try {
        await renderAdminDashboard();
    } catch (error) {
        log(error.message);
    }
});

document.getElementById('refresh-admin-loans')?.addEventListener('click', async () => {
    try {
        await renderAdminLoans();
    } catch (error) {
        log(error.message);
    }
});

document.getElementById('refresh-admin-payments')?.addEventListener('click', async () => {
    try {
        await renderAdminPayments();
    } catch (error) {
        log(error.message);
    }
});

document.getElementById('generate-ojk-report')?.addEventListener('click', async () => {
    if (state.user?.role !== 'admin') {
        log('Login sebagai admin untuk generate laporan OJK.');
        return;
    }

    try {
        await api('/api/admin/ojk-report', { method: 'POST' });
        log('Laporan OJK otomatis berhasil dibuat.');
        await renderAdminDashboard();
    } catch (error) {
        log(error.message);
    }
});

document.addEventListener('click', async (event) => {
    const moduleButton = event.target.closest('[data-complete-module]');
    const approveButton = event.target.closest('[data-approve-loan]');
    const rejectButton = event.target.closest('[data-reject-loan]');
    const paymentStatusButton = event.target.closest('[data-update-payment-status]');
    const deleteCommunityPostButton = event.target.closest('[data-delete-community-post]');

    if (moduleButton) {
        try {
            await api(`/api/learning-modules/${moduleButton.dataset.completeModule}/complete`, { method: 'POST' });
            log('Modul belajar ditandai selesai.');
            await renderLearning();
        } catch (error) {
            log(error.message);
        }
    }

    if (approveButton) {
        if (state.user?.role !== 'admin') {
            log('Aksi ini hanya untuk admin.');
            return;
        }

        try {
            await api(`/api/loans/${approveButton.dataset.approveLoan}/status`, {
                method: 'PATCH',
                body: JSON.stringify({
                    status: 'disbursed',
                    admin_notes: 'Pengajuan disetujui, dana siap dicairkan, dan akses belajar dibuka.',
                }),
            });
            log('Pengajuan disetujui dan dicairkan.');
            await Promise.allSettled([renderAdminDashboard(), renderAdminLoans()]);
        } catch (error) {
            log(error.message);
        }
    }

    if (rejectButton) {
        if (state.user?.role !== 'admin') {
            log('Aksi ini hanya untuk admin.');
            return;
        }

        try {
            await api(`/api/loans/${rejectButton.dataset.rejectLoan}/status`, {
                method: 'PATCH',
                body: JSON.stringify({
                    status: 'rejected',
                    admin_notes: 'Pengajuan ditolak admin.',
                }),
            });
            log('Pengajuan ditolak admin.');
            await Promise.allSettled([renderAdminDashboard(), renderAdminLoans()]);
        } catch (error) {
            log(error.message);
        }
    }

    if (paymentStatusButton) {
        if (state.user?.role !== 'admin') {
            log('Aksi ini hanya untuk admin.');
            return;
        }

        const status = paymentStatusButton.dataset.paymentStatus;

        try {
            await api(`/api/payments/${paymentStatusButton.dataset.updatePaymentStatus}/status`, {
                method: 'PATCH',
                body: JSON.stringify({
                    status,
                    admin_notes: status === 'verified'
                        ? 'Pembayaran diverifikasi admin.'
                        : 'Pembayaran ditolak admin.',
                }),
            });
            log(status === 'verified' ? 'Pembayaran diverifikasi admin.' : 'Pembayaran ditolak admin.');
            await Promise.allSettled([renderAdminDashboard(), renderAdminLoans(), renderAdminPayments()]);
        } catch (error) {
            log(error.message);
        }
    }

    if (deleteCommunityPostButton) {
        if (state.user?.role !== 'admin') {
            log('Aksi ini hanya untuk admin.');
            return;
        }

        if (!confirm('Hapus posting komunitas ini?')) {
            return;
        }

        try {
            await api(`/api/community-posts/${deleteCommunityPostButton.dataset.deleteCommunityPost}`, { method: 'DELETE' });
            log('Posting komunitas berhasil dihapus.');
            await renderCommunity();
        } catch (error) {
            log(error.message);
        }
    }
});

hydrateAuth();
renderHome().catch((error) => log(error.message));
refreshAll().catch((error) => log(error.message));
