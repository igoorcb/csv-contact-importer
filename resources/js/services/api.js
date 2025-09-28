import axios from 'axios';

axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const api = {
    uploadCsv(file) {
        const formData = new FormData();
        formData.append('csv_file', file);

        return axios.post('/imports/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },

    getImportStatus(importId) {
        return axios.get(`/imports/${importId}/status`);
    },

    getImportSummary(importId) {
        return axios.get(`/imports/${importId}/summary`);
    },

    getContacts(page = 1, perPage = 10) {
        return axios.get(`/contacts?page=${page}&per_page=${perPage}`);
    },
};

export default api;
