<template>
    <div>
        <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
            <div class="container">
                <router-link :to="{ name: 'dashboard' }" class="navbar-brand"
                    >DASHBOARD</router-link
                >
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div
                    class="collapse navbar-collapse"
                    id="navbarSupportedContent"
                >
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item" v-if="!loggedIn">
                            <router-link
                                :to="{ name: 'login' }"
                                class="nav-link active"
                                aria-current="page"
                                >Login</router-link
                            >
                        </li>
                        <li class="nav-item" v-if="loggedIn">
                            <span class="nav-link active" aria-current="page">{{
                                user.name
                            }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!--- render router view -->
        <!-- <router-view></router-view> -->
        <router-view @login="handleLogin" @logout="handleLogout"></router-view>
    </div>
</template>

<script>
import api from "./api";

export default {
    name: "App",
    data() {
        return {
            loggedIn: localStorage.getItem("loggedIn"),
            token: localStorage.getItem("token"),
            user: [],
        };
    },
    mounted() {
        if (this.loggedIn) {
            this.getUserData();
            this.$router.push({ name: "dashboard" });
        }
    },
    methods: {
        getUserData() {
            api.get("/api/users/current", {
                headers: { Authorization: this.token },
            })
                .then((response) => {
                    // console.log(response);
                    this.user = response.data.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        handleLogin() {
            this.loggedIn = true;
            this.token = localStorage.getItem("token");
            this.getUserData();
        },
        handleLogout() {
            this.loggedIn = false;
            this.token = null;
            this.user = [];
        },
    },
};
</script>
