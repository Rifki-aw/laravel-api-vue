<script setup>
import { ref } from "vue";
import Api from "../../../api";
import { useRouter, useRoute } from "vue-router";

// init router
const router = useRouter();
const route = useRoute();

// token login
const token = ref(localStorage.getItem("token"));

// define state
const addresses = ref({
    street: "",
    city: "",
    province: "",
    country: "",
    postal_code: "",
});
const errors = ref([]);

// method untuk handle perubahan file
const storeAdress = async () => {
    try {
        await Api.post(
            `/api/contacts/${route.params.id}/addresses`,
            {
                street: addresses.value.street,
                city: addresses.value.city,
                province: addresses.value.province,
                country: addresses.value.country,
                postal_code: addresses.value.postal_code
            },
            {
                headers: {
                    Authorization: token.value,
                },
            }
        );
        router.push({ name: "address-index" });
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
                    :to="{ name: 'address-index' }"
                    class="btn btn-md btn-success rounded shadow border-0 mb-3"
                >
                    Kembali
                </router-link>
            </div>
            <div class="col-md-12">
                <div class="card border-0 rounded shadow">
                    <div class="card-body">
                        <form @submit.prevent="storeAdress()">
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold"
                                    >Street</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="addresses.street"
                                    placeholder="Street"
                                />
                                <div
                                    class="mt-2 alert alert-danger alert-dismissible fade show"
                                    role="alert"
                                    v-if="errors.street"
                                >
                                    <strong>{{ errors.street[0] }}</strong>
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
                                    >City</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="addresses.city"
                                    placeholder="City"
                                />
                                <div
                                    class="alert alet-danger mt-2"
                                    v-if="errors.city"
                                >
                                    <span>{{ errors.city[0] }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold"
                                    >Province</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="addresses.province"
                                    placeholder="Province"
                                />
                                <div
                                    class="mt-2 alert alert-danger alert-dismissible fade show"
                                    role="alert"
                                    v-if="errors.province"
                                >
                                    <strong>{{ errors.province[0] }}</strong>
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
                                    >Country</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="addresses.country"
                                    placeholder="Country"
                                />
                                <div
                                    class="alert alet-danger mt-2"
                                    v-if="errors.country"
                                >
                                    <span>{{ errors.country[0] }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold"
                                    >Postal Code</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="addresses.postal_code"
                                    placeholder="Postal Code"
                                />
                                <div
                                    class="alert alet-danger mt-2"
                                    v-if="errors.postal_code"
                                >
                                    <span>{{ errors.postal_code[0] }}</span>
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
