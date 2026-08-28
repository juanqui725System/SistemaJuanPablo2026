import {api} from '../utils/api.js';

const renderEstado = (estado) => {
    const badge = {
        activo: 'bg-emerald-100 text-emerald-700',
        inactivo: 'bg-gray-100 text-gray-500',
        suspendido: 'bg-rose-100 text-rose-700',
    };
    const key = String(estado ?? '').toLowerCase();
    const cls = badge[key] || 'bg-gray-100 text-gray-500';
    const label = estado ?? 'Desconocido';
    return `<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
};

const handleView = (user) => {
    alert(`Ver usuario: ${user.username}`);
};

const handleDelete = async (id, row) => {
    if (!confirm('¿Está seguro de eliminar este usuario?')) return;
    try {
        await api.del(`users/${id}`);
        row.remove();
    } catch (error) {
        alert('Error al eliminar el usuario');
    }
};

const onClick = (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;
    const { action, id, username } = btn.dataset;
    if (action === 'view') {
        handleView({ id, username });
    } else if (action === 'delete') {
        handleDelete(id, btn.closest('tr'));
    }
};

export const getUserList = async () => {
    const container = document.getElementById('userTableList');
    container.innerHTML = '<tr><td class="px-6 py-8 text-center text-gray-400" colspan="4">Cargando...</td></tr>';
    container.addEventListener('click', onClick);
    try {
        const users = await api.get('users');
        container.innerHTML = users.map(user => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-900">${user.id}</td>
                <td class="px-6 py-4 text-sm text-gray-900 font-medium">${user.username}</td>
                <td class="px-6 py-4">${renderEstado(user.estado)}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button data-action="view" data-id="${user.id}" data-username="${user.username}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                            Ver
                        </button>
                        <button data-action="delete" data-id="${user.id}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors">
                            Eliminar
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error al obtener la lista de usuarios:', error);
        container.innerHTML = '<tr><td class="px-6 py-8 text-center text-rose-500" colspan="4">Error al cargar los usuarios</td></tr>';
    }
};
