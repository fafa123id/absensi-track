<script setup>
import { ref, computed, watch } from "vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import { showSuccess } from "@/Composables/swal";

const props = defineProps({
    departments: Array,
});

const emit = defineEmits([
    "open-add",
    "open-edit",
    "open-employees",
    "delete",
    "refresh-token",
]);

const search = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(5);

const filteredDepartments = computed(() => {
    currentPage.value = 1;
    visibleTokenId.value = null;
    copiedTokenId.value = null;
    if (!search.value) {
        return props.departments;
    }
    return props.departments.filter((dept) =>
        dept.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const paginatedDepartments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredDepartments.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredDepartments.value.length / itemsPerPage.value);
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
    }
};
</script>

<template>
    <div
        class="bg-white overflow-x-auto shadow-sm sm:rounded-lg p-8 flex flex-col"
    >
        <h1 class="text-2xl font-bold mb-4 text-center">
            Daftar Departemen
            <PrimaryButton
                @click="$emit('open-add')"
                class="bg-green-600 hover:bg-green-700"
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

        <TextInput
            v-model="search"
            type="text"
            class="w-full md:w-1/2 self-center mb-8"
            placeholder="Cari Departemen..."
        />

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Departemen</th>
                    <th class="px-6 py-3 text-left">Token Departemen</th>
                    <th class="px-6 py-3 text-left">Detail</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    v-for="department in paginatedDepartments"
                    :key="department.id"
                >
                    <td class="px-6 py-4">{{ department.name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <input
                                :type="
                                    visibleTokenId === department.id
                                        ? 'text'
                                        : 'password'
                                "
                                :value="department.token"
                                readonly
                                class="flex-grow p-1 border rounded bg-gray-100 focus:outline-none"
                            />
                            <button
                                @click="toggleVisibility(department.id)"
                                class="text-gray-500 hover:text-gray-800"
                            >
                                <svg
                                    v-if="visibleTokenId === department.id"
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
                                    copyToken(department.token, department.id)
                                "
                                class="text-gray-500 hover:text-gray-800"
                            >
                                <svg
                                    v-if="copiedTokenId === department.id"
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
                    <td class="px-6 py-4 space-x-2">
                        <PrimaryButton
                            @click="$emit('open-employees', department)"
                            >Karyawan</PrimaryButton
                        >
                        <PrimaryButton
                            @click="$emit('open-employees', department)"
                            >Jobdesk</PrimaryButton
                        >
                    </td>
                    <td class="flex flex-row px-6 py-4 gap-2">
                        <PrimaryButton
                            @click="$emit('open-edit', department)"
                            class="bg-blue-600 hover:bg-blue-700"
                            >Edit</PrimaryButton
                        >
                        <PrimaryButton
                            @click="
                                $emit('delete', department.id, department.name)
                            "
                            class="bg-red-600 hover:bg-red-700"
                            >Hapus</PrimaryButton
                        >
                        <PrimaryButton
                            @click="
                                $emit(
                                    'refresh-token',
                                    department.id,
                                    department.name
                                )
                            "
                            class="bg-green-600 hover:bg-green-700"
                            >Refresh</PrimaryButton
                        >
                    </td>
                </tr>
                <tr v-if="paginatedDepartments.length === 0">
                    <td colspan="4" class="text-center py-4 text-gray-500">
                        Tidak ada departemen yang cocok dengan pencarian Anda.
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- <div class="mt-6 flex justify-center items-center space-x-4" v-if="totalPages > 1">
            <SecondaryButton @click="currentPage--" :disabled="currentPage === 1">
                &laquo; Sebelumnya
            </SecondaryButton>
            <span class="text-gray-600">Halaman {{ currentPage }} dari {{ totalPages }}</span>
            <SecondaryButton @click="currentPage++" :disabled="currentPage === totalPages">
                Selanjutnya &raquo;
            </SecondaryButton>
        </div> -->
        <Pagination
            :totalPages="totalPages"
            :currentPage="currentPage"
            @page-changed="currentPage = $event"
            class="mt-6"
        />
    </div>
</template>
