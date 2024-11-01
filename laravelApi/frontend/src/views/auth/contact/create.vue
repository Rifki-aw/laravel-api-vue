<script setup>
import { ref, onMounted } from "vue";
import Api from "../../../api";
import { useRouter } from "vue-router";

// init router
const router = useRouter();

// token login
const token = ref(localStorage.getItem("token"));

// define state
const contact = ref({
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
});
const errors = ref([]);

// method untuk handle perubahan file
const storeContact = async () => {
    try {
        await Api.post(
            "/api/contacts",
            {
                first_name: contact.value.first_name,
                last_name: contact.value.last_name,
                email: contact.value.email,
                phone: contact.value.phone,
            },
            {
                headers: {
                    Authorization: token.value,
                },
            }
        );
        router.push({ name: "contact" });
    } catch (error) {
        if (error.response.status === 400) {
            errors.value = error.response.data.errors;
        } else if (error.response.status === 401) {
            errors.value = { message: ["Unauthorized"] };
        }
    }
};
</script>

<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <router-link
                    :to="{ name: 'contact' }"
                    class="btn btn-md btn-success rounded shadow border-0 mb-3"
                >
                    Halaman Kontak
                </router-link>
            </div>
            <div class="col-md-12">
                <div class="card border-0 rounded shadow">
                    <div class="card-body">
                        <form @submit.prevent="storeContact()">
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold"
                                    >First Name</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="contact.first_name"
                                    placeholder="First Name"
                                />
                                <div
                                    class="mt-2 alert alert-danger alert-dismissible fade show"
                                    role="alert"
                                    v-if="errors.first_name"
                                >
                                    <strong>{{ errors.first_name[0] }}</strong>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="Close"
                                    ></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold"
                                    >Last Name</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="contact.last_name"
                                    placeholder="Last Name"
                                />
                                <div
                                    class="alert alet-danger mt-2"
                                    v-if="errors.last_name"
                                >
                                    <span>{{ errors.last_name[0] }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold"
                                    >Email</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="contact.email"
                                    placeholder="email@gmail.com"
                                />
                                <div
                                    class="mt-2 alert alert-danger alert-dismissible fade show"
                                    role="alert"
                                    v-if="errors.email"
                                >
                                    <strong>{{ errors.email[0] }}</strong>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="Close"
                                    ></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold"
                                    >Phone</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="contact.phone"
                                    placeholder="Phone Number"
                                />
                                <div
                                    class="alert alet-danger mt-2"
                                    v-if="errors.phone"
                                >
                                    <span>{{ errors.phone[0] }}</span>
                                </div>
                            </div>
                            <button
                                type="submit"
                                class="btn btn-md btn-primary rounded-sm shadow border-0"
                            >
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
