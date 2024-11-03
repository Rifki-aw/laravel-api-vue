<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import Api from "../../../api";

const route = useRoute();
const token = ref(localStorage.getItem("token"));
const emit = defineEmits(['logout']);

const addresses = ref([]);
// const errors = ref([]);

const fetchDataAddress = async () => {
    try {
        const response = await Api.get(`api/contacts/${route.params.id}/addresses`, {
            headers: {
                Authorization: token.value
            }
        });
        addresses.value = response.data.data;
        // console.log(addresses.value);
    } catch (error) {
        console.error("Failed to fetch address: ", error);
    }
}

onMounted(() => {
    fetchDataAddress();
})

async function deleteAddress(contact_id, address_id) {
    try {
        if (confirm("Anda yakin ingin menghapus kontak ini?")) {
            await Api.delete(`/api/contacts/${contact_id}/addresses/${address_id}`, {
                headers: { Authorization: token.value }
            });
            fetchDataAddress();
        }
    } catch (error) {
        console.error("Delete failed:", error);
    }
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
                                        <router-link
                                            :to="{ name: 'address-create' }"
                                            class="btn btn-md btn-success rounded shadow border-0 mb-3"
                                        >
                                            Add New Address
                                        </router-link>
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
                                                <router-link
                                                    v-if="address.id && route.params.id"
                                                    :to="{
                                                        name: 'address-edit',
                                                        params: {
                                                            id: route.params.id,
                                                            idAddress: address.id
                                                        },
                                                    }"
                                                    class="btn btn-sm btn-primary rounded-sm shadow border-0 me-2"
                                                >
                                                    EDIT
                                                </router-link>
                                                <button
                                                    v-if="address.id"
                                                    @click="deleteAddress(route.params.id, address.id)"
                                                    class="btn btn-sm btn-danger rounded-sm shadow border-0 me-2"
                                                >
                                                    DELETE
                                                </button>
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
