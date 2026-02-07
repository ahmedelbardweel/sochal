/**
 * AbsScroll API Bridge
 * Handles communication between Frontend and Laravel API
 */

const API_BASE = '/api/v1';

const bridge = {
    // Token Management
    setToken(token) {
        localStorage.setItem('abs_token', token);
    },
    getToken() {
        return localStorage.getItem('abs_token');
    },
    clearToken() {
        localStorage.removeItem('abs_token');
    },

    // Request Wrapper
    async request(endpoint, options = {}) {
        const url = `${API_BASE}${endpoint}`;
        const token = this.getToken();

        const isFormData = options.body instanceof FormData || (options.body && typeof options.body.append === 'function');
        const headers = {
            ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            ...(token && { 'Authorization': `Bearer ${token}` }),
            ...(options.headers || {})
        };

        // Always include credentials to ensure the Laravel session cookie is sent/received
        options.credentials = 'include';

        try {
            const response = await fetch(url, { ...options, headers });

            // Handle non-JSON responses (like 419 HTML page)
            const contentType = response.headers.get("content-type");
            let data;
            if (contentType && contentType.includes("application/json")) {
                data = await response.json();
            } else {
                data = { message: await response.text() };
            }

            if (!response.ok) {
                if (response.status === 419) {
                    throw { status: 419, message: 'CSRF token mismatch. Please refresh the page.' };
                }
                throw { status: response.status, ...data };
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    // Auth Shortcuts
    async login(email, password) {
        const data = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
        if (data.token) this.setToken(data.token);
        return data;
    },

    async register(formData) {
        const data = await this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify(formData)
        });
        if (data.token) this.setToken(data.token);
        return data;
    },

    async me() {
        return await this.request('/auth/me');
    },

    async logout() {
        await this.request('/auth/logout', { method: 'POST' });
        this.clearToken();
        window.location.href = '/login';
    }
};

window.bridge = bridge;
