import './bootstrap';
import Alpine from 'alpinejs';

window.parseApiResponse = async function (response) {
    const body = await response.text();

    if (!body) {
        return {
            code: response.status,
            message: response.ok ? 'Success' : 'Empty response from server',
            data: null,
            error_code: response.ok ? null : 'INVALID_API_RESPONSE',
            errors: null,
        };
    }

    try {
        return JSON.parse(body);
    } catch {
        return {
            code: response.status,
            message: 'Server returned an invalid response',
            data: null,
            error_code: 'INVALID_API_RESPONSE',
            errors: null,
        };
    }
};

window.Alpine = Alpine;
Alpine.start();
