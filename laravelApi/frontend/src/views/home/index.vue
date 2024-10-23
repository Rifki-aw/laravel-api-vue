<!-- Versi 3.X -->
<script setup>
import { ref, onMounted } from "vue";
import api from "../../api";
import { useRouter } from "vue-router";

const router = useRouter();

const token = ref(localStorage.getItem("token"));
const loggedIn = ref(localStorage.getItem("loggedIn"));
const user = ref({});

const emit = defineEmits(["logout"]);

onMounted(async () => {
    if (!loggedIn.value) {
        router.push({ name: "login" });
        console.log(loggedIn.value);
    }
    try {
        const response = await api.get("/api/users/current", {
            headers: { Authorization: token.value },
        });
        user.value = response.data.data;
    } catch (error) {
        if (error.response.status === 401) {
            router.push({ name: "login" });
        } else {
            console.log(error);
        }
    }
});

function logout() {
    api.delete("/api/users/logout", {
        headers: { Authorization: token.value },
    })
        .then((response) => {
            console.log("Logout success:", response.data.data);

            // remove localStorage item
            localStorage.removeItem("token");
            localStorage.removeItem("loggedIn");

            emit("logout");

            // redirect to login page
            router.push({ name: "login" });
        })
        .catch((error) => {
            console.error("Logout failed:", error);
        });
}
</script>

<template>
    <div class="dashboard" style="margin-top: 80px">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            MAIN MENU
                            <hr />
                            <ul class="list-group">
                                <router-link
                                    :to="{ name: 'dashboard' }"
                                    class="list-group-item text-dark text-decoration-none"
                                    >DASHBOARD</router-link
                                >
                                <router-link
                                    :to="{ name: 'contact' }"
                                    class="list-group-item text-dark text-decoration-none"
                                >
                                    CONTACT
                                </router-link>
                                <li
                                    @click="logout"
                                    class="list-group-item text-dark text-decoration-none"
                                    style="cursor: pointer"
                                >
                                    LOGOUT
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            DASHBOARD
                            <hr />
                            Selamat Datang <strong>{{ user.name }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Versi 2.X  -->
<!-- <script>
import api from "../../api";

export default {
    name: "Dashboard",
    data() {
        return {
            loggedIn: localStorage.getItem("loggedIn"),
            token: localStorage.getItem("token"),
            user: [],
        };
    },
    created() {
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
    methods: {
        logout() {
            api.delete("/api/users/logout", {
                headers: { Authorization: this.token },
            })
                .then((response) => {
                    console.log("Logout success:", response.data.data);

                    // remove localStorage item
                    localStorage.removeItem("token");
                    localStorage.removeItem("loggedIn");

                    // redirect to login page
                    this.$router.push({ name: "login" });
                })
                .catch((error) => {
                    console.error("Logout failed:", error);
                });
        },
    },
    // mounted() {},
};
</script> -->
