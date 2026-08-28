import { getUserList } from './components/UserList.js';

const app = document.getElementById('app');
const pageTitle = document.getElementById('pageTitle');
const navLinks = document.querySelectorAll('[data-view]');

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
        const res = await fetch('./src/views/user.html');
        app.innerHTML = await res.text();
        pageTitle.textContent = titles.users;
        setActiveLink('users');
        const res=await getUserList();
        console.log("Resultado",res);
    },
    products: async () => {
        const res = await fetch('./src/views/products.html');
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
        }
    });
});

views.home();
