const rawConfig = window.LogicAsData || {};

const routePrefix = String(rawConfig.routePrefix || '');
const normalizedPrefix = routePrefix.startsWith('/') ? routePrefix : `/${routePrefix}`;

const config = Object.freeze({
    ...rawConfig,
    routePrefix: normalizedPrefix
});

export function useConfig() {
    return config;
}
