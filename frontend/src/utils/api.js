// const API_URL = 'http://api.sexto.com/';
const API_URL = 'https://sistemajuanpablo2026.onrender.com/';
export const api={
    //funcion para obtener data de la api
    get: async (endpoint) => {
        try {
            const response = await fetch(`${API_URL}${endpoint}`);
            if (!response.ok) {
                throw new Error(`error! status: ${response.status}`);
            }
            return await response.json();
          }catch (error) {
            console.error('Error al obtener data:', error);
            throw error;
          }
    },

    post: async (endpoint, data) => {
        const response = await fetch(`${API_URL}${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) {
            const validationErrors = result.errores
                ? Object.values(result.errores).join(' ')
                : '';
            throw new Error(validationErrors || result.message || `Error al guardar (${response.status})`);
        }
        return result;
    },

    del: async (endpoint) => {
        try {
            const response = await fetch(`${API_URL}${endpoint}`, { method: 'DELETE' });
            if (!response.ok) {
                throw new Error(`error! status: ${response.status}`);
            }
            return await response.json();
        }catch (error) {
            console.error('Error al eliminar data:', error);
            throw error;
        }
    },

};
