const API_URL = 'http://api.sistemasexto/';

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