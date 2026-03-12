import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Setup CSRF token for axios globally
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    window.axios.defaults.headers.common['X-XSRF-TOKEN'] = csrfToken;
}

// CSRF token handling is now managed by CSRFService.js
// This prevents conflicts with multiple CSRF handling mechanisms
