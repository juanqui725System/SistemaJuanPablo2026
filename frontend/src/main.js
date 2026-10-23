import { getUserList } from './components/UserList.js';
const app = document.getElementById('app');
const pageTitle = document.getElementById('pageTitle');
const navLinks = document.querySelectorAll('[data-view]');
const sidebar = document.getElementById('sidebar');
const menuToggle = document.getElementById('menuToggle');
const menuBackdrop = document.getElementById('menuBackdrop');
const currentUsername = document.getElementById('currentUsername');
const sessionAction = document.getElementById('sessionAction');
const sessionActionText = document.getElementById('sessionActionText');
const sessionActionIcon = document.getElementById('sessionActionIcon');

let loggedInUser = null;
try {
    loggedInUser = JSON.parse(localStorage.getItem('sexto_usuario') || 'null');
} catch {
    localStorage.removeItem('sexto_usuario');
}

if (loggedInUser?.username) {
    currentUsername.textContent = loggedInUser.username;
    currentUsername.classList.remove('hidden');
    sessionAction.href = '#';
    sessionAction.setAttribute('aria-label', 'Cerrar sesión');
    sessionActionText.textContent = 'Cerrar sesión';
    sessionActionIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h4m7-4 5-5-5-5m5 5H9" />';
    sessionAction.addEventListener('click', (event) => {
        event.preventDefault();
        localStorage.removeItem('sexto_usuario');
        window.location.assign('./src/views/login.html');
    });
}

function closeMobileMenu() {
    sidebar.classList.add('-translate-x-full');
    menuBackdrop.classList.add('hidden');
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.setAttribute('aria-label', 'Abrir menÃº');
}

function toggleMobileMenu() {
    const isOpen = !sidebar.classList.contains('-translate-x-full');
    sidebar.classList.toggle('-translate-x-full', isOpen);
    menuBackdrop.classList.toggle('hidden', isOpen);
    menuToggle.setAttribute('aria-expanded', String(!isOpen));
    menuToggle.setAttribute('aria-label', isOpen ? 'Abrir menÃº' : 'Cerrar menÃº');
}

menuToggle.addEventListener('click', toggleMobileMenu);
menuBackdrop.addEventListener('click', closeMobileMenu);
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeMobileMenu();
});
window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
    if (event.matches) closeMobileMenu();
});

const titles = {
    home: 'Dashboard',
    users: 'Usuarios',
    products: 'Productos'
};

function setActiveLink(view) {
    navLinks.forEach(link => {
        link.classList.remove('bg-indigo-600', 'text-white');
        link.classList.add('text-slate-300', 'hover:bg-slate-700', 'hover:text-white');
        if (link.dataset.view === view) {
            link.classList.add('bg-indigo-600', 'text-white');
            link.classList.remove('text-slate-300', 'hover:bg-slate-700', 'hover:text-white');
        }
    });
}

const views = {
    home: async () => {
        const res = await fetch('./src/views/home.html');
        app.innerHTML = await res.text();
        pageTitle.textContent = titles.home;
        setActiveLink('home');
    },
    users: async () => {
        const res = await fetch('./src/views/user.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        pageTitle.textContent = titles.users;
        setActiveLink('users');
        await getUserList();
    },
    products: async () => {
        const res = await fetch('./src/views/products.html', { cache: 'no-store' });
        app.innerHTML = await res.text();
        pageTitle.textContent = titles.products;
        setActiveLink('products');
    }
};

navLinks.forEach(link => {
    link.addEventListener('click', async (event) => {
        event.preventDefault();
        const view = link.dataset.view;
        if (views[view]) {
            await views[view]();
            closeMobileMenu();
        }
    });
});

views.home();
