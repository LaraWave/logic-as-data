import { createRouter, createWebHistory } from 'vue-router';
import { useConfig } from '@/composables/useConfig';

const { routePrefix } = useConfig();

const routes = [
    {
        path: '/logic-rules',
        component: () => import('./pages/LogicRules/RulesList.vue'),
        name: 'logic-rules.index',
        meta: { title: 'Logic Rules Management' }
    },
    {
        path: '/logic-rules/create',
        component: () => import('./pages/LogicRules/RuleBuilder.vue'),
        name: 'logic-rules.create' 
    },
    {
        path: '/logic-rules/:id/edit',
        component: () => import('./pages/LogicRules/RuleBuilder.vue'),
        name: 'logic-rules.edit',
        props: true
    }
];

export default createRouter({
    history: createWebHistory(routePrefix),
    routes
});
