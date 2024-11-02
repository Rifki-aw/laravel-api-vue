<script setup>
import { onMounted, reactive, ref } from "vue";
import api from "../../api";
import { useRouter } from "vue-router";

const router = useRouter();

const validation = ref([]);
const registerFailed = ref(null);

const user = reactive({
    username: "",
    password: "",
    name: "",
});

const register = async () => {
    try {
        const response = await api.post("/api/users", {
            username: user.username,
            password: user.password,
            name: user.name,
        });
        // console.log(response.data);
        router.push({ name: "login" });
    } catch (error) {
        if (error.response.status === 400) {
            if (error.response.data.errors.username) {
                registerFailed.value = "Username sudah digunakan!";
            } else {
                validation.value = error.response.data.errors;
            }
        } else {
            registerFailed.value = "Registrasi gagal";
        }
    }
};

// onMounted(() => {
//   if (localStorage.getItem("loggedIn")) {
//     router.push({ path: from.path }); // kembali ke halaman sebelumnya
//   }
// });

</script>

<template>
    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 rounded shadow">
                    <div class="card-body">
                        <div v-if="registerFailed">
                            <div
                                class="mt-2 alert alert-warning alert-dismissible fade show"
                                role="alert"
                            >
                                <strong>{{ registerFailed }}</strong>
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close"
                                ></button>
                            </div>
                        </div>
                        <h4>Register</h4>
                        <hr />
                        <form @submit.prevent="register">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input
                                    type="text"
                                    v-model="user.name"
                                    class="form-control"
                                    id="name"
                                    placeholder="Name"
                                />
                            </div>
                            <div
                                class="mt-2 alert alert-danger"
                                v-if="validation.name"
                            >
                                {{ validation.name[0] }}
                            </div>

                            <div class="form-group mt-2">
                                <label for="username">Username</label>
                                <input
                                    type="text"
                                    v-model="user.username"
                                    class="form-control"
                                    id="username"
                                    placeholder="Username"
                                    autocomplete="off"
                                />
                            </div>
                            <div v-if="registerFailed">
                                <div
                                    class="mt-2 alert alert-warning alert-dismissible fade show"
                                    role="alert"
                                >
                                    <strong>{{ registerFailed }}</strong>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="Close"
                                    ></button>
                                </div>
                            </div>
                            <div
                                class="mt-2 alert alert-danger"
                                v-if="validation.username"
                            >
                                {{ validation.username[0] }}
                            </div>
                            <div class="form-group mt-2">
                                <label for="password">Password</label>
                                <input
                                    type="password"
                                    v-model="user.password"
                                    class="form-control"
                                    id="password"
                                    placeholder="Password"
                                    autocomplete="off"
                                />
                            </div>
                            <div
                                class="mt-2 alert alert-danger"
                                v-if="validation.password"
                            >
                                {{ validation.password[0] }}
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">
                                Register
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
