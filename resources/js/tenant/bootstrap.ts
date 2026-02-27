import axios from 'axios';

const tenantId = window.location.pathname.split('/')[2];

const api = axios.create({
    baseURL: `/app/${tenantId}/api`,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true, // Crucial para Sanctum
});

export default api;