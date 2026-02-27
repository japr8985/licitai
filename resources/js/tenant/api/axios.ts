import axios, { AxiosResponse, InternalAxiosRequestConfig } from 'axios';
import type { ApiResponse } from '../types/api';

const api = axios.create({
    baseURL: `/app/${window.location.pathname.split('/')[2]}/api`,
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest', // Marca la petición como AJAX para Laravel
    }
});
api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
        // A. Inyección de CSRF Token (Vital para Laravel Sanctum/Session)
        // Laravel guarda el token en una cookie llamada 'XSRF-TOKEN'.
        // Debemos leerla y ponerla en el header 'X-XSRF-TOKEN'.
        const token = document.cookie
            .split('; ')
            .find(row => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];

        if (token) {
            config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
        }

        // B. (Opcional) Inyección de Bearer Token 
        // Si en el futuro usas tokens manuales guardados en localStorage:
        const authToken = sessionStorage.getItem('token');
        if (authToken) {
           config.headers.Authorization = `Bearer ${authToken}`;
        }

        return config;
});
api.interceptors.response.use(
    (response: AxiosResponse<ApiResponse>) => {
        // 1. ESCAPE HATCH: Si es un archivo, lo dejamos pasar intacto
        // Verificamos si es una instancia de Blob o si la config pedía un blob
        if (response.data instanceof Blob || response.config.responseType === 'blob') {
            return response;
        }
        // Si success es false (pero el status fue 200), lanzamos error
        if (!response.data.success) {
            return Promise.reject(response.data.message);
        }
        // Devolvemos solo el contenido para no tener que hacer .data.content en cada componente
        return response;
    },
    error => {
        // DEBUG: Esto te dirá qué está fallando antes de que llegue al componente
        console.error("Axios Interceptor interceptó un error real:", error);

        // Si el servidor respondió con un error (4xx, 5xx)
        if (error.response) {
            return Promise.reject(error.response);
        } 
        // Si no hubo respuesta (Error de red/CORS)
        else if (error.request) {
            return Promise.reject(new Error("No response from server. Check connection."));
        }
        
        // Error genérico
        return Promise.reject(error || new Error("Unknown Protocol Error"));
    }
);

export default api;