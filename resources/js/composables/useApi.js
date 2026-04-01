import { useConfig } from '@/composables/useConfig';

export function useApi() {
    const { routePrefix } = useConfig();

    const request = async (endpoint, options = {}) => {
        let url = `${routePrefix}/api/${endpoint}`;
        
        if (options.params && Object.keys(options.params).length > 0) {
            const queryString = new URLSearchParams(options.params).toString();
            url += `?${queryString}`;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(url, {
            method: options.method || 'GET',
            body: options.body,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                ...(options.headers || {})
            }
        });

        if (!response.ok) {
            let errorData = { message: `${response.status}: ${response.statusText}`, errors: {} };
            try {
                const data = await response.json();
                errorData = { ...errorData, ...data };
            } catch (e) {}

            const error = new Error(errorData.message);
            error.status = response.status;
            error.errors = errorData.errors;
            throw error;
        }

        return await response.json();
    };

    return {
        get: (endpoint, params = {}) => request(endpoint, { method: 'GET', params }),
        post: (endpoint, body) => request(endpoint, { method: 'POST', body: JSON.stringify(body) }),
        put: (endpoint, body) => request(endpoint, { method: 'PUT', body: JSON.stringify(body) }),
        patch: (endpoint, body) => request(endpoint, { method: 'PATCH', body: JSON.stringify(body) }),
        del: (endpoint) => request(endpoint, { method: 'DELETE' })
    };
}