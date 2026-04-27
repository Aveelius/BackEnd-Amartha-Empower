import './bootstrap';

const state = {
    token: null,
    user: null,
    logs: [],
};

const STORAGE_KEY = 'amartha-empower-auth';

const el = {
    authStatus: document.getElementById('auth-status'),
    homeFeatureButtons: document.getElementById('home-feature-buttons'),
    homeQuickCards: document.getElementById('home-quick-cards'),
    userDashboard: document.getElementById('user-dashboard'),
    learningList: document.getElementById('learning-list'),
    communityList: document.getElementById('community-list'),
    adminDashboard: document.getElementById('admin-dashboard'),
    systemLog: document.getElementById('system-log'),
    loanResult: document.getElementById('loan-result'),
};

const formatCurrency = (value) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);

const setLoanMessage = (message, isError = false) => {
    if (!el.loanResult) {
        return;
    }

    el.loanResult.textContent = message;
    el.loanResult.style.borderLeft = isError ? '6px solid #ff726d' : '6px solid #35b8aa';
};

const persistAuth = () => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify({
        token: state.token,
        user: state.user,
    }));
};

const clearPersistedAuth = () => {
    localStorage.removeItem(STORAGE_KEY);
};

const hydrateAuth = () => {
    const raw = localStorage.getItem(STORAGE_KEY);

    if (!raw) {
        return;
    }

    try {
        const parsed = JSON.parse(raw);
        state.token = parsed.token || null;
        state.user = parsed.user || null;
    } catch {
        clearPersistedAuth();
    }
};

const log = (message) => {
    if (!el.systemLog) {
        return;
    }

    state.logs.unshift(`${new Date().toLocaleTimeString('id-ID')} - ${message}`);
    el.systemLog.innerHTML = state.logs.slice(0, 8).map((item) => `<div class="log-item">${item}</div>`).join('');
};

const api = async (url, options = {}) => {
    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
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
            <div>
                <div class="meta-row"><span>Progres Pelunasan</span><strong>${summary.repayment_progress}%</strong></div>
                <div class="progress-bar"><span style="width:${summary.repayment_progress}%"></span></div>
            </div>
            <div class="stack-list">
                ${data.notifications.map((item) => `<div class="stack-item"><strong>${item.title}</strong><p>${item.message}</p></div>`).join('')}
            </div>
        `
        : 'Belum ada pengajuan pinjaman. Isi formulir Ajukan Pinjaman Cepat di atas.';

    log('Dashboard user diperbarui dari API.');
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

        return `
            <div class="tile">
                <strong>${item.title}</strong>
                <p>${item.summary}</p>
                <div class="meta-row">
                    <span>${item.duration_label}</span>
                    <span>${completion}% selesai</span>
                </div>
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
                <strong>${item.title}</strong>
                <span>${item.category}</span>
            </div>
            <p>${item.content}</p>
            <div class="meta-row">
                <span>Oleh ${item.user.name}</span>
                <span>${item.event_date || item.created_at.slice(0, 10)}</span>
            </div>
            ${item.event_location ? `<p>Lokasi: ${item.event_location}</p>` : ''}
            ${item.comments.length ? `<div class="stack-list">${item.comments.map((comment) => `<div class="mini-card"><strong>${comment.user.name}</strong><p>${comment.content}</p></div>`).join('')}</div>` : ''}
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
            ${data.recent_applications.map((item) => `
                <div class="stack-item">
                    <div class="meta-row">
                        <strong>${item.application_code}</strong>
                        <span>${item.status}</span>
                    </div>
                    <p>${item.user.name} mengajukan ${formatCurrency(item.amount)} tenor ${item.tenor_months} bulan.</p>
                    <button class="ghost-button" data-approve-loan="${item.id}">Setujui & Cairkan</button>
                </div>
            `).join('')}
        </div>
        <div class="stack-item">
            <strong>Laporan OJK Terbaru</strong>
            <p>${data.latest_ojk_report ? `${data.latest_ojk_report.report_date} | Active loans ${data.latest_ojk_report.active_loans} | Outstanding ${formatCurrency(data.latest_ojk_report.total_outstanding)}` : 'Belum ada laporan.'}</p>
        </div>
    `;

    log('Dashboard admin berhasil dimuat.');
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
    await Promise.allSettled([renderUserDashboard(), renderLearning(), renderCommunity(), renderAdminDashboard()]);
};

document.getElementById('register-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = Object.fromEntries(new FormData(event.currentTarget).entries());

    try {
        const data = await api('/api/auth/register', { method: 'POST', body: JSON.stringify(formData) });
        state.token = data.token;
        state.user = data.user;
        persistAuth();
        log(`Registrasi berhasil untuk ${data.user.name}.`);
        setLoanMessage('Registrasi berhasil. Anda sudah bisa mengajukan pinjaman.', false);
        await refreshAll();
    } catch (error) {
        log(error.message);
    }
});

document.getElementById('login-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = Object.fromEntries(new FormData(event.currentTarget).entries());

    try {
        const data = await api('/api/auth/login', { method: 'POST', body: JSON.stringify(formData) });
        state.token = data.token;
        state.user = data.user;
        persistAuth();
        log(`Login berhasil sebagai ${data.user.role}.`);
        setLoanMessage('Login berhasil. Form Quick Loan sudah aktif.', false);
        await refreshAll();
    } catch (error) {
        log(error.message);
    }
});

document.getElementById('logout-button')?.addEventListener('click', async () => {
    if (state.token) {
        try {
            await api('/api/auth/logout', { method: 'POST' });
        } catch (error) {
            log(error.message);
        }
    }

    state.token = null;
    state.user = null;
    clearPersistedAuth();
    setLoanMessage('', false);
    log('Sesi login dihapus.');
    await refreshAll();
});

document.querySelectorAll('[data-demo-login]').forEach((button) => {
    button.addEventListener('click', async () => {
        const role = button.dataset.demoLogin;
        const payload = { login: role === 'admin' ? '081111111111' : '081234567890', password: 'password' };

        try {
            const data = await api('/api/auth/login', { method: 'POST', body: JSON.stringify(payload) });
            state.token = data.token;
            state.user = data.user;
            persistAuth();
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

    const form = new FormData(event.currentTarget);
    const payload = {
        amount: Number(form.get('amount')),
        tenor_months: Number(form.get('tenor_months')),
        documents: [
            { document_type: 'KTP', file_name: form.get('document_ktp') },
            { document_type: 'Foto Usaha', file_name: form.get('document_usaha') },
        ],
    };

    try {
        const data = await api('/api/loans', { method: 'POST', body: JSON.stringify(payload) });
        setLoanMessage(`Pengajuan ${data.data.application_code} terkirim dengan status ${data.data.status}.`, false);
        log('Pengajuan pinjaman cepat berhasil dikirim.');
        await refreshAll();
    } catch (error) {
        setLoanMessage(error.message, true);
        log(error.message);
    }
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
            await renderAdminDashboard();
        } catch (error) {
            log(error.message);
        }
    }
});

hydrateAuth();
renderHome().catch((error) => log(error.message));
refreshAll().catch((error) => log(error.message));
