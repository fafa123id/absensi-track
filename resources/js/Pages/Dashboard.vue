<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import NavLink from "@/Components/NavLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { confirmAction, showSuccess } from "@/Composables/swal";
import { ref, nextTick } from "vue";
import AddForm from "@/Components/Departements/AddForm.vue";
import EmployeeList from "@/Components/Departements/EmployeeList.vue";
defineProps({
    departements: Object,
    company: Object,
});

const visibleTokenId = ref(null);

const copiedTokenId = ref(null);

const toggleVisibility = (departmentId) => {
    visibleTokenId.value =
        visibleTokenId.value === departmentId ? null : departmentId;
};

const copyToken = async (token, departmentId) => {
    try {
        await navigator.clipboard.writeText(token);
        await showSuccess(
            "Berhasil Menyalin Token!",
            "Token telah disalin ke clipboard."
        );

        copiedTokenId.value = departmentId;
        setTimeout(() => {
            copiedTokenId.value = null;
        }, 2000);
    } catch (err) {
        console.error("Gagal menyalin token: ", err);
        alert("Gagal menyalin token.");
    }
};
const form = useForm({});

const deleteDepartment = async (id, name) => {
    const result = await confirmAction(
        "Hapus Departemen " + name + "?",
        "Semua data terkait akan ikut terhapus!"
    );
    if (result.isConfirmed) {
        form.delete(route("departements.destroy", id), {
            preserveScroll: true,
        });
    }
};

const refreshToken = async (id, name) => {
    const result = await confirmAction(
        "Segarkan Token untuk Departemen " + name + "?",
        "Token baru akan dihasilkan untuk departemen ini.",
        "info"
    );
    if (result.isConfirmed) {
        form.patch(route("departements.refreshToken", id), {
            preserveScroll: true,
        });
    }
};
const confirmingAddDepartment = ref(false);

const openAddModal = () => {
    confirmingAddDepartment.value = true;
};

const closeAddModal = () => {
    confirmingAddDepartment.value = false;
};

const isEmployeeListVisible = ref(false);

const selectedDepartment = ref(null);

const openListEmployeeModal = (department) => {
    selectedDepartment.value = department;
    isEmployeeListVisible.value = true;
};

const closeListEmployeeModal = () => {
    isEmployeeListVisible.value = false;
    setTimeout(() => {
        selectedDepartment.value = null;
    }, 300);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>
        <AddForm :show="confirmingAddDepartment" @close="closeAddModal" />
        <EmployeeList
            :show="isEmployeeListVisible"
            :department="selectedDepartment"
            @close="closeListEmployeeModal"
        />
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <h1 class="text-2xl font-bold mb-4 text-center">
                        Informasi Perusahaan
                    </h1>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    Nama Perusahaan
                                </th>
                                <th class="px-6 py-3 text-left">
                                    Email Perusahaan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4">{{ company.name }}</td>
                                <td class="px-6 py-4">{{ company.email }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <h1 class="text-2xl font-bold mb-4 text-center">
                        Daftar Departemen
                        <PrimaryButton
                            @click="openAddModal"
                            class="bg-green-600 hover:bg-green-700 flex items-center justify-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                        </PrimaryButton>
                    </h1>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    Nama Departemen
                                </th>
                                <th class="px-6 py-3 text-left">
                                    Token Departemen
                                </th>
                                <th class="px-6 py-3 text-left">Detail</th>
                                <th class="px-6 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="departement in departements.data"
                                :key="departement.id"
                            >
                                <td class="px-6 py-4">
                                    {{ departement.name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <input
                                            :type="
                                                visibleTokenId ===
                                                departement.id
                                                    ? 'text'
                                                    : 'password'
                                            "
                                            :value="departement.token"
                                            readonly
                                            class="flex-grow p-1 border rounded bg-gray-100 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        />
                                        <button
                                            @click="
                                                toggleVisibility(departement.id)
                                            "
                                            class="text-gray-500 hover:text-gray-800"
                                        >
                                            <svg
                                                v-if="
                                                    visibleTokenId ===
                                                    departement.id
                                                "
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-5 h-5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243L6.228 6.228"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-5 h-5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="
                                                copyToken(
                                                    departement.token,
                                                    departement.id
                                                )
                                            "
                                            class="text-gray-500 hover:text-gray-800"
                                        >
                                            <svg
                                                v-if="
                                                    copiedTokenId ===
                                                    departement.id
                                                "
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-5 h-5 text-green-500"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-5 h-5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <PrimaryButton
                                        @click="
                                            openListEmployeeModal(departement)
                                        "
                                    >
                                        Lihat Karyawan
                                    </PrimaryButton>
                                </td>
                                <td class="flex flex-row px-6 py-4 gap-2">
                                    <PrimaryButton
                                        v-if="departement.name !== 'Master'"
                                        :href="
                                            route(
                                                'departements.edit',
                                                departement.id
                                            )
                                        "
                                        class="bg-blue-600 hover:bg-blue-700"
                                    >
                                        Edit
                                    </PrimaryButton>
                                    <form
                                        v-if="departement.name !== 'Master'"
                                        @submit.prevent="
                                            deleteDepartment(
                                                departement.id,
                                                departement.name
                                            )
                                        "
                                    >
                                        <PrimaryButton
                                            class="bg-red-600 hover:bg-red-700"
                                            :class="{
                                                'opacity-25': form.processing,
                                            }"
                                            :disabled="form.processing"
                                        >
                                            Hapus
                                        </PrimaryButton>
                                    </form>
                                    <form
                                        @submit.prevent="
                                            refreshToken(
                                                departement.id,
                                                departement.name
                                            )
                                        "
                                    >
                                        <PrimaryButton
                                            class="bg-green-600 hover:bg-green-700"
                                            :class="{
                                                'opacity-25': form.processing,
                                            }"
                                            :disabled="form.processing"
                                        >
                                            Refresh Token
                                        </PrimaryButton>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="p-6 flex items-center justify-center">
                        <Pagination :links="departements.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
