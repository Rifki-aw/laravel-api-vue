<script setup>
import { ref, onMounted, watch } from "vue";
import api from "../../api";
import { useRouter } from "vue-router";

// Mendapatkan instance rotuer untuk navigasi
const router = useRouter();

// Menyimpan/mendapatkan token dari localStorage
const token = ref(localStorage.getItem("token"));

// Menyimpan daftar kontak yang diterima dari API
const contacts = ref([]);

// Event logout untuk diemit saat usuer logout
const emit = defineEmits(['logout']);

// Membuat ref untuk menyimpan query search kontak
const searchQuery = ref("");

// Fungsi untuk mengambil data kontak dari API
const fetchDataContacts = async () => {
    try {
        // Request ke API untuk mendapatkan kontak dengan filter nama dari searchQuery
        const response = await api.get("api/contacts", {
            headers: { Authorization: token.value }, 
            params: { name: searchQuery.value, } // Menyertakan search query sebagai parameter
        });
        // Isi data yang didapat dari response API
        contacts.value = response.data.data;
    } catch (error) {
        console.error("Failed to fetch contacts: ", error);
    }
};

// lifecycle hook yang dijalankan ketika komponen pertama kali di mount
onMounted(() => {
    fetchDataContacts();
});

// Watcher untuk memantauu perubahan pada searchQuery
watch(searchQuery, () => {
    // Memanggil fungsi fetchDataContacts setiap kali searchQuery berubah
    fetchDataContacts();
});

function logout() {
    // Melakukan request delete ke endpoint logout API
    api.delete("/api/users/logout", {
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

async function deleteContact(id) {
    try {
        if (confirm("Anda yakin ingin menghapus kontak ini?")) {
            await api.delete(`api/contacts/${id}`, {
                headers: { Authorization: token.value },
            });
            fetchDataContacts();
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
                            List Contact
                            <hr />
                            <div class="container">
                                <div class="row py-2">
                                    <div class="col-md-6">
                                        <router-link :to="{ name: 'create' }" class="btn btn-md btn-success rounded shadow border-0 mb-3">
                                            Add New Contact
                                        </router-link>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Search..." v-model="searchQuery">
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Firstname</th>
                                            <th>Lastname</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="contacts.length === 0">
                                            <td colspan="5" class="text-center">User not Found</td>
                                        </tr>
                                        <tr v-for="contact in contacts" :key="contact.id">
                                            <td>{{ contact.first_name }}</td>
                                            <td>{{ contact.last_name }}</td>
                                            <td>{{ contact.email }}</td>
                                            <td>{{ contact.phone }}</td>
                                            <td class="text-center">
                                                <router-link 
                                                v-if="contact.id"
                                                :to="{ name: 'edit', params: {id: contact.id } }" class="btn btn-sm btn-primary rounded-sm shadow border-0 me-2">
                                                    EDIT
                                                </router-link>
                                                <button 
                                                v-if="contact.id"
                                                @click="deleteContact(contact.id)" class="btn btn-sm btn-danger rounded-sm shadow border-0 me-2">
                                                    DELETE
                                                </button>
                                                <button class="btn btn-sm btn-warning rounded-sm shadow border-0">
                                                    DETAIL
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
