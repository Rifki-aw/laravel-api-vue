import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../views/home/index.vue')
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/auth/login.vue')
    },
];

// create router
const router = createRouter({
    history: createWebHistory(),
    routes // <-- routes,
});

export default router;