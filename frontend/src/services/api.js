import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api', // Endereço padrão do seu backend Laravel
});

export default api;