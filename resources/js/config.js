const rawConfig = window.LogicAsData || {
    routePrefix: 'logic-as-data'
};

export const config = {
    get basePrefix() {
        const prefix = rawConfig.routePrefix;
        return prefix.startsWith('/') ? prefix : `/${prefix}`;
    },
    get apiBase() {
        return `${this.basePrefix}/api`;
    }
};

export default config;
