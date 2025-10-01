<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { confirmAction } from "@/Composables/swal";
import { ref } from "vue";

import DepartmentList from "@/Components/Departements/DepartementList.vue";
import AddForm from "@/Components/Departements/AddForm.vue";
import EmployeeList from "@/Components/Departements/EmployeeList.vue";
import EditForm from "@/Components/Departements/EditForm.vue";

const props = defineProps({
    departements: Array, 
    company: Object,
});

const confirmingAddDepartment = ref(false);
const confirmingEditDepartment = ref(false);
const isEmployeeListVisible = ref(false);
const selectedDepartment = ref(null);

const form = useForm({});

const openAddModal = () => (confirmingAddDepartment.value = true);
const closeAddModal = () => (confirmingAddDepartment.value = false);

const openEditModal = (department) => {
    selectedDepartment.value = department;
    confirmingEditDepartment.value = true;
};
const closeEditModal = () => {
    confirmingEditDepartment.value = false;
    selectedDepartment.value = null;
};

const openListEmployeeModal = (department) => {
    selectedDepartment.value = department;
    isEmployeeListVisible.value = true;
};
const closeListEmployeeModal = () => {
    isEmployeeListVisible.value = false;
    selectedDepartment.value = null;
};

const deleteDepartment = async (id, name) => {
    const result = await confirmAction(
        "Hapus Departemen " + name + "?",
        "Semua data terkait akan ikut terhapus!"
    );
    if (result.isConfirmed) {
        form.delete(route("departements.destroy", id), {
            preserveState: true,
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
            preserveState: true,
            preserveScroll: true,
        });
    }
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
        <EditForm
            :show="confirmingEditDepartment"
            :department="selectedDepartment"
            @close="closeEditModal"
        />
        <EmployeeList
            :show="isEmployeeListVisible"
            :department="selectedDepartment"
            :departements="props.departements"
            @close="closeListEmployeeModal"
        />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg p-8">
                    <h1 class="text-2xl font-bold mb-4 text-center">
                        Informasi Perusahaan
                    </h1>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-left">Telepon</th>
                                <th class="px-6 py-3 text-left">Alamat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4">{{ company.name }}</td>
                                <td class="px-6 py-4">{{ company.email }}</td>
                                <td class="px-6 py-4">{{ company.phone }}</td>
                                <td class="px-6 py-4">{{ company.address }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <DepartmentList
                    :departments="props.departements"
                    @open-add="openAddModal"
                    @open-edit="openEditModal"
                    @open-employees="openListEmployeeModal"
                    @delete="deleteDepartment"
                    @refresh-token="refreshToken"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>