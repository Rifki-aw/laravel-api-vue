import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/dashboard",
        name: "dashboard",
        component: () => import("../views/home/index.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/contact",
        name: "contact",
        component: () => import("../views/auth/contact.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/create",
        name: "create",
        component: () => import("../views/auth/contact/create.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/register",
        name: "register",
        component: () => import("../views/auth/register.vue"),
    },
    {
        path: "/login",
        name: "login",
        component: () => import("../views/auth/login.vue"),
    },
];

// create router
const router = createRouter({
    history: createWebHistory(),
    routes, // <-- routes,
});

router.beforeEach((to, from, next) => {
    if (to.name === "login" || to.name === "register") {
        if (localStorage.getItem("loggedIn")) {
            next({ name: "dashboard" });
        } else {
            next();
        }
    } else if (to.meta.requiresAuth && !localStorage.getItem("loggedIn")) {
        next({ name: "login", query: { redirect: to.path } });
    } else {
        next();
    }
});

export default router;
