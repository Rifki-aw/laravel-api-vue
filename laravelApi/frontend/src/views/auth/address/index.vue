<script setup>
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import Api from "../../../api";

const route = useRoute();
const router = useRouter();
const token = ref(localStorage.getItem("token"));
const emit = defineEmits(['logout']);

const addresses = ref({
    street: "",
    city: "",
    province: "",
    country: "",
    postal_code: "",
});
// const errors = ref([]);

const fetchDataAddress = async () => {
    try {
        const response = await Api.get(`api/contacts/${route.params.id}/addresses`, {
            headers: {
                Authorization: token.value
            }
        });
        addresses.value = response.data.data;
    } catch (error) {
        console.error("Failed to fetch address: ", error);
    }
}

onMounted(() => {
    fetchDataAddress();
})

function logout() {
    // Melakukan request delete ke endpoint logout API
    Api.delete("/api/users/logout", {
        headers: { Authorization: token.value },
    })
        .then((response) => {
            console.log("Logout success:", response.data.data);

            // Menghapus token dan status login dari localStorage
            localStorage.removeItem("token");
            localStorage.removeItem("loggedIn");

            // Emit event logout untuk memberi tahu komponen lain bahwa user telah logout
            emit("logout");

            // Redirect user ke halaman login menggunakan router
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
                <div class="col-md-3">
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
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            List Address
                            <hr />
                            <div class="container">
                                <div class="row py-2">
                                    <div class="col-md-6">
                                        <!-- <router-link
                                            :to="{ name: 'create' }"
                                            class="btn btn-md btn-success rounded shadow border-0 mb-3"
                                        >
                                            Add New Address
                                        </router-link> -->
                                    </div>
                                </div>

                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Street</th>
                                            <th>City</th>
                                            <th>Province</th>
                                            <th>Country</th>
                                            <th>Postal Code</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="addresses.length === 0">
                                            <td colspan="6" class="text-center">
                                                Not Found
                                            </td>
                                        </tr>
                                        <tr
                                            v-for="address in addresses"
                                            :key="address.id"
                                        >
                                            <td>{{ address.street }}</td>
                                            <td>{{ address.city }}</td>
                                            <td>{{ address.province }}</td>
                                            <td>{{ address.country }}</td>
                                            <td>{{ address.postal_code }}</td>
                                            <td class="text-center">
                                                <!-- <router-link
                                                    v-if="contact.id"
                                                    :to="{
                                                        name: 'edit',
                                                        params: {
                                                            id: contact.id,
                                                        },
                                                    }"
                                                    class="btn btn-sm btn-primary rounded-sm shadow border-0 me-2"
                                                >
                                                    EDIT
                                                </router-link> -->
                                                <!-- <button
                                                    v-if="contact.id"
                                                    @click="
                                                        deleteContact(
                                                            contact.id
                                                        )
                                                    "
                                                    class="btn btn-sm btn-danger rounded-sm shadow border-0 me-2"
                                                >
                                                    DELETE
                                                </button> -->
                                                <!-- <router-link
                                                    v-if="contact.id"
                                                    :to="{
                                                        name: 'address-index',
                                                        params: {
                                                            id: contact.id,
                                                        },
                                                    }"
                                                    class="btn btn-sm btn-warning rounded-sm shadow border-0"
                                                >
                                                    DETAIL
                                                </router-link> -->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
