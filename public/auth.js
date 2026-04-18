const AUTH_STORAGE_KEY = 'konevaAuthSession';
const USERS_STORAGE_KEY = 'konevaUsers';

function safeParse(jsonValue, fallback) {
    try {
        return JSON.parse(jsonValue);
    } catch (_) {
        return fallback;
    }
}

function getUsers() {
    return safeParse(localStorage.getItem(USERS_STORAGE_KEY), []);
}

function saveUsers(users) {
    localStorage.setItem(USERS_STORAGE_KEY, JSON.stringify(users));
}

function getSession() {
    return safeParse(localStorage.getItem(AUTH_STORAGE_KEY), null);
}

function setSession(user) {
    const session = {
        name: user.name,
        email: user.email,
        role: user.role,
        loginAt: new Date().toISOString()
    };
    localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(session));
}

function clearSession() {
    localStorage.removeItem(AUTH_STORAGE_KEY);
}

function updateAuthVisibility() {
    const session = getSession();
    const authTargets = document.querySelectorAll('[data-auth]');

    authTargets.forEach((el) => {
        const state = el.getAttribute('data-auth');
        if (state === 'guest') {
            el.hidden = !!session;
        } else if (state === 'user') {
            el.hidden = !session;
        }
    });

    const userNameEl = document.querySelector('[data-user-name]');
    const userRoleEl = document.querySelector('[data-user-role]');
    if (session && userNameEl) userNameEl.textContent = session.name;
    if (session && userRoleEl) userRoleEl.textContent = session.role;
}

function requireAuth() {
    const requiresAuth = document.body.hasAttribute('data-require-auth');
    if (!requiresAuth) return;

    const session = getSession();
    if (!session) {
        window.location.href = '/login?next=portal';
    }
}

function handleLogout() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (!logoutBtn) return;

    logoutBtn.addEventListener('click', () => {
        clearSession();
        updateAuthVisibility();
        window.location.href = '/login';
    });
}

function handleAuthForms() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const statusBox = document.getElementById('authStatus');

    const showStatus = (message, isError) => {
        if (!statusBox) return;
        statusBox.textContent = message;
        statusBox.classList.toggle('error', !!isError);
        statusBox.classList.add('show');
    };

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            const name = String(formData.get('name') || '').trim();
            const email = String(formData.get('email') || '').trim().toLowerCase();
            const password = String(formData.get('password') || '');
            const role = String(formData.get('role') || 'client');

            const users = getUsers();
            const exists = users.some((user) => user.email === email);
            if (exists) {
                showStatus('Email sudah terdaftar. Silakan login.', true);
                return;
            }

            const user = { name, email, password, role };
            users.push(user);
            saveUsers(users);
            setSession(user);
            showStatus('Akun berhasil dibuat. Mengalihkan ke portal...', false);

            setTimeout(() => {
                window.location.href = '/portal';
            }, 600);
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            const email = String(formData.get('email') || '').trim().toLowerCase();
            const password = String(formData.get('password') || '');

            const users = getUsers();
            const user = users.find((candidate) => {
                return candidate.email === email && candidate.password === password;
            });

            if (!user) {
                showStatus('Email atau password tidak sesuai.', true);
                return;
            }

            setSession(user);
            showStatus('Login berhasil. Mengalihkan ke portal...', false);
            setTimeout(() => {
                window.location.href = '/portal';
            }, 450);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    requireAuth();
    updateAuthVisibility();
    handleAuthForms();
    handleLogout();
});
