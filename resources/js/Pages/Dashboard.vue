<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import { confirmAction } from "@/Composables/swal";
import { ref, computed, onUnmounted } from "vue";

import DepartmentList from "@/Components/Departements/DepartementList.vue";
import AddForm from "@/Components/Departements/AddForm.vue";
import EmployeeList from "@/Components/Departements/EmployeeList.vue";
import EditForm from "@/Components/Departements/EditForm.vue";
import ProjectList from "@/Components/Project/ProjectList.vue";
import ProjectRoleList from "@/Components/Project/Role/ProjectRoleList.vue";
import CompanyWifi from "@/Components/Management/Wifi/CompanyWifi.vue";

const props = defineProps({
    departements: Array,
    company: Object,
    projects: Array,
});

const confirmingAddDepartment = ref(false);
const confirmingEditDepartment = ref(false);
const isEmployeeListVisible = ref(false);
const selectedDepartment = ref(null);

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

window.Echo.private(`Dashboard.${props.company.id}`).listen(
    "updatedDashboardData",
    (e) => {
        console.log("Event Dashboard diterima:", e);
        refreshData();
    }
);
onUnmounted(() => {
    window.Echo.leave(`Dashboard.${props.company.id}`);
});
const form = useForm({});

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

// --- STATE MANAGEMENT UNTUK PROYEK ---
const selectedProjectId = ref(null);
const isProjectRoleModalVisible = ref(false);
const projectForModal = computed(() => {
    if (!selectedProjectId.value) return null;
    return props.projects.find((p) => p.id === selectedProjectId.value);
});
const departementForModal = computed(() => {
    if (!selectedDepartment.value) return null;
    return props.departements.find((d) => d.id === selectedDepartment.value.id);
});
const openProjectRoleModal = (project) => {
    console.log('Dashboard: Event "open-roles" diterima!', project);
    selectedProjectId.value = project.id;
    isProjectRoleModalVisible.value = true;
};
const closeProjectRoleModal = () => {
    isProjectRoleModalVisible.value = false;
    selectedProjectId.value = null;
};

// --- FUNGSI REFRESH DATA GLOBAL ---
const refreshData = () => {
    router.reload({
        only: ["projects", "departements", "company"],
        preserveState: true,
        preserveScroll: true,
    });
    console.log("Data telah direfresh dari server.");
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
            :department="departementForModal"
            :departements="props.departements"
            @close="closeListEmployeeModal"
            @need-update="refreshData"
        />
        <ProjectRoleList
            :show="isProjectRoleModalVisible"
            :project="projectForModal"
            @close="closeProjectRoleModal"
            @data-changed="refreshData"
        />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div
                    class="bg-white overflow-x-auto shadow-sm sm:rounded-lg p-8"
                >
                    <h1
                        v-if="$page.props.auth.user.is_admin"
                        class="text-3xl font-bold mb-4 text-center"
                    >
                        Halo Greater Admin {{ $page.props.auth.user.name }}!
                    </h1>
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
                />
                <ProjectList
                    :projects="props.projects"
                    :company="props.company"
                    @open-roles="openProjectRoleModal"
                />
                <CompanyWifi
                    :Wifis= "props.company.wifis"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
