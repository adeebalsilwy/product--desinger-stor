/**
 * Global CSRF Token Handler
 * 
 * Provides centralized CSRF token management for all AJAX requests
 * Automatically handles token extraction, validation, and renewal
 */

// Get CSRF token from meta tag
export function getCsrfTokenFromMeta() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        const token = metaTag.getAttribute('content');
        console.log('CSRF token from meta tag:', token ? 'FOUND' : 'NOT FOUND');
        return token;
    }
    console.log('CSRF meta tag not found');
    return null;
}

// Get CSRF token from cookie
export function getCsrfTokenFromCookie() {
    const name = 'XSRF-TOKEN';
    const value = '; ' + document.cookie;
    const parts = value.split('; ' + name + '=');
    if (parts.length === 2) {
        const token = decodeURIComponent(parts.pop().split(';').shift());
        console.log('CSRF token from cookie:', token ? 'FOUND' : 'NOT FOUND');
        return token;
    }
    console.log('CSRF token not found in cookie');
    return null;
}

// Get CSRF token (tries meta tag first, then cookie)
export function getCsrfToken() {
    const token = getCsrfTokenFromMeta() || getCsrfTokenFromCookie();
    console.log('Final CSRF token:', token ? 'FOUND' : 'NOT FOUND');
    return token;
}

// Get CSRF token param name
export function getCsrfParam() {
    const metaTag = document.querySelector('meta[name="csrf-param"]');
    if (metaTag) {
        return metaTag.getAttribute('content');
    }
    return '_token';
}

// Setup axios defaults (if using axios)
export function setupAxiosCSRF() {
    if (typeof axios !== 'undefined') {
        const token = getCsrfToken();
        if (token) {
            axios.defaults.headers.common['X-XSRF-TOKEN'] = token;
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        }
    }
}

// Create fetch wrapper with automatic CSRF handling
export async function csrfFetch(url, options = {}) {
    let token = getCsrfToken();
    
    // Ensure headers object exists
    if (!options.headers) {
        options.headers = {};
    }
    
    // Add CSRF token to headers - Laravel expects X-XSRF-TOKEN for AJAX requests
    if (token) {
        // For XHR requests, Laravel looks for X-XSRF-TOKEN header
        options.headers['X-XSRF-TOKEN'] = token;
        // Also add the standard X-Requested-With header
        options.headers['X-Requested-With'] = 'XMLHttpRequest';
    }
    
    // Set default credentials
    if (!options.credentials) {
        options.credentials = 'same-origin';
    }
    
    console.log('=== CSRF Fetch Debug ===');
    console.log('URL:', url);
    console.log('Method:', options.method);
    console.log('CSRF Token:', token ? 'FOUND' : 'NOT FOUND');
    console.log('Headers:', options.headers);
    console.log('Body type:', options.body ? options.body.constructor.name : 'NO BODY');
    
    try {
        const response = await fetch(url, options);
        
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        // Handle CSRF token expiration (419 status)
        if (response.status === 419) {
            console.warn('CSRF Token Expired - Attempting to refresh...');
            
            // Try to refresh the token automatically
            const refreshed = await refreshCsrfToken();
            if (refreshed) {
                // Retry the original request with new token
                token = getCsrfToken();
                if (token) {
                    options.headers['X-XSRF-TOKEN'] = token;
                }
                
                console.log('Retrying request with refreshed CSRF token...');
                console.log('New token:', token ? 'FOUND' : 'NOT FOUND');
                const retryResponse = await fetch(url, options);
                console.log('Retry response status:', retryResponse.status);
                return retryResponse;
            } else {
                // If refresh failed, show error and suggest refresh
                console.error('Failed to refresh CSRF token');
                window.dispatchEvent(new CustomEvent('csrf-token-expired', {
                    detail: { url, options }
                }));
                throw new Error('CSRF Token Expired - Please refresh the page');
            }
        }
        
        return response;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// Create FormData with CSRF token
export function createCSRFFormData() {
    const formData = new FormData();
    const token = getCsrfToken();
    const param = getCsrfParam();
    
    if (token && param) {
        formData.append(param, token);
    }
    
    return formData;
}

// Refresh CSRF token from server
export async function refreshCsrfToken() {
    try {
        console.log('=== CSRF Token Refresh Started ===');
        
        // First, try to get a fresh token from a dedicated API endpoint
        // If that doesn't exist, fall back to fetching the main page
        let newToken = null;
        
        try {
            // Try to get token from a dedicated API endpoint
            console.log('Attempting to refresh CSRF token from API endpoint...');
            const tokenResponse = await fetch('/api/csrf-token', { 
                method: 'GET', 
                credentials: 'same-origin'
            });
            
            if (tokenResponse.ok) {
                const tokenData = await tokenResponse.json();
                newToken = tokenData.token;
                console.log('CSRF token from API endpoint:', newToken ? 'SUCCESS' : 'FAILED');
            } else {
                console.log('API endpoint returned status:', tokenResponse.status);
            }
        } catch (apiError) {
            console.debug('API endpoint for CSRF token not available, falling back to HTML parsing');
        }
        
        // If the API approach didn't work, try parsing HTML from the main page
        if (!newToken) {
            try {
                console.log('Attempting to refresh CSRF token from HTML parsing...');
                const response = await fetch('/', { 
                    method: 'GET', 
                    credentials: 'same-origin' 
                });
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                newToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('CSRF token from HTML parsing:', newToken ? 'SUCCESS' : 'FAILED');
            } catch (htmlError) {
                console.error('Failed to refresh CSRF token from HTML:', htmlError);
            }
        }
        
        // If we still don't have a token, try to get it from the current page
        if (!newToken) {
            newToken = getCsrfTokenFromMeta();
            console.log('CSRF token from current page meta:', newToken ? 'FOUND' : 'NOT FOUND');
        }
        
        if (newToken) {
            console.log('Updating CSRF token in DOM and cookies...');
            
            // Update meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                const oldToken = metaTag.getAttribute('content');
                metaTag.setAttribute('content', newToken);
                console.log('Updated meta tag token from', oldToken, 'to', newToken);
            } else {
                console.log('Creating new meta tag for CSRF token');
                const newMeta = document.createElement('meta');
                newMeta.name = 'csrf-token';
                newMeta.content = newToken;
                document.head.appendChild(newMeta);
            }
            
            // Update cookie if it exists
            const cookieName = 'XSRF-TOKEN';
            const existingCookie = document.cookie.split(';').find(cookie => cookie.trim().startsWith(cookieName));
            if (existingCookie) {
                const cookieValue = `${cookieName}=${encodeURIComponent(newToken)}; path=/; samesite=lax`;
                document.cookie = cookieValue;
                console.log('Updated CSRF cookie');
            }
            
            // Update axios if available
            if (typeof axios !== 'undefined' && axios.defaults) {
                axios.defaults.headers.common['X-XSRF-TOKEN'] = newToken;
                console.log('Updated axios CSRF token');
            }
            
            console.log('CSRF token refreshed successfully');
            return true;
        }
        console.error('Failed to obtain new CSRF token from any source');
        return false;
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
        return false;
    }
}

// Auto-refresh CSRF token on page focus
export function setupAutoRefreshCSRF() {
    let lastRefresh = Date.now();
    const REFRESH_INTERVAL = 4 * 60 * 1000; // 4 minutes (slightly less than Laravel's default 5 minutes)
    
    document.addEventListener('visibilitychange', async () => {
        if (document.visibilityState === 'visible') {
            const now = Date.now();
            if (now - lastRefresh > REFRESH_INTERVAL) {
                const success = await refreshCsrfToken();
                if (success) {
                    lastRefresh = now;
                }
            }
        }
    });
    
    // Also refresh on page focus (tab switching)
    window.addEventListener('focus', async () => {
        const now = Date.now();
        if (now - lastRefresh > REFRESH_INTERVAL) {
            const success = await refreshCsrfToken();
            if (success) {
                lastRefresh = now;
            }
        }
    });
}

// Initialize CSRF protection globally
export function initCSRFProtection() {
    // Setup axios if available
    setupAxiosCSRF();
    
    // Auto-refresh token
    setupAutoRefreshCSRF();
    
    // Handle token expiration globally
    window.addEventListener('csrf-token-expired', (event) => {
        if (confirm('Your session has expired. Would you like to refresh the page?')) {
            window.location.reload();
        }
    });
    
    console.log('CSRF Protection initialized');
}

// Export all functions
export default {
    getCsrfTokenFromMeta,
    getCsrfTokenFromCookie,
    getCsrfToken,
    getCsrfParam,
    setupAxiosCSRF,
    csrfFetch,
    createCSRFFormData,
    refreshCsrfToken,
    setupAutoRefreshCSRF,
    initCSRFProtection
};
