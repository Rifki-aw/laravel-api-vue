<script setup>
import { ref, onMounted, defineEmits } from "vue";
import api from "../../api";
import { useRouter } from "vue-router";

const router = useRouter();

const loggedIn = ref(localStorage.getItem("loggedIn"));
const user = ref([]);
const validation = ref([]);
const loginFailed = ref(null);

const emit = defineEmits(["login"]);

const login = async () => {
    try {
        const response = await api.post("/api/users/login", {
            username: user.value.username,
            password: user.value.password,
        });

        if (response.data.data.token) {
            localStorage.setItem("loggedIn", "true");
            localStorage.setItem("token", response.data.data.token);

            emit("login");
            router.push({ name: "dashboard" });
        }
    } catch (error) {
        if (error.response.status === 401) {
            loginFailed.value = true;
        } else {
            console.log(error);
        }
    }

    validation.value = [];

    if (!user.value.username) {
        validation.value.username = true;
    }
    if (!user.value.password) {
        validation.value.password = true;
    }
};

onMounted(() => {
    if (loggedIn.value) {
        router.push({ name: "dashboard" });
    }
});
</script>

<template>
    <div class="login">
        <div class="container" style="margin-top: 100px">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div v-if="loginFailed" class="alert-danger alert">
                        Username atau Password Anda Salah
                    </div>
                    <div class="card">
                        <div class="card-body">
                            LOGIN
                            <hr />
                            <form @submit.prevent="login">
                                <div class="form-group">
                                    <label for="username">USERNAME</label>
                                    <input
                                        type="text"
                                        class="form-control mb-2"
                                        placeholder="Masukkan Username"
                                        v-model="user.username"
                                    />
                                    <div
                                        class="mt-2 alert alert-danger"
                                        v-if="validation.username"
                                    >
                                        Masukkan Username
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password">PASSWORD</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        placeholder="Masukkan Password"
                                        v-model="user.password"
                                    />
                                    <div
                                        v-if="validation.password"
                                        class="mt-2 alert alert-danger"
                                    >
                                        Masukkan Password
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="submit" class="btn btn-primary mt-3">
                                        LOGIN
                                    </button>
                                    <div class="text-center mt-3">
                                        <a href="" class="text-muted text-decoration-none">Register</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- <script>
import api from "../../api";

export default {
    name: "login",
    data() {
        return {
            loggedIn: localStorage.getItem("loggedIn"),
            token: localStorage.getItem("token"),
            user: [],
            validation: [],
            loginFailed: null,
        };
    },
    methods: {
        login() {
            api.post("/api/users/login", {
                username: this.user.username,
                password: this.user.password,
            })
                .then((response) => {
                    //debug cookie
                    console.log(response);

                    if (response.data.data.token) {
                        // set localStorage untuk menandai user telah login
                        localStorage.setItem("loggedIn", "true"),
                            // set localStorage token pada user yang login
                            localStorage.setItem(
                                "token",
                                response.data.data.token
                            );

                        // change state
                        // this.loggedIn = true;
                        this.$emit("login");

                        // redirect ke dashboard
                        return this.$router.push({ name: "dashboard" });
                    }
                    // else {
                    //     // set state login failed
                    //     this.loginFailed = true;
                    // }
                })
                .catch((error) => {
                    if (error.response.status === 401) {
                        this.loginFailed = true;
                    } else {
                        console.log(error);
                    }
                });

            this.validation = [];

            if (!this.user.username) {
                this.validation.username = true;
            }
            if (!this.user.password) {
                this.validation.password = true;
            }
        },

        mounted() {
            if (this.loggedIn) {
                return this.$router.push({ name: "dashboard" });
            }
        },
    },
};
</script> -->
