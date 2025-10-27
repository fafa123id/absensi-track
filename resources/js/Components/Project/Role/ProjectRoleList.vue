<script setup>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AddRoleForm from "@/Components/Project/Role/AddRoleForm.vue";
import EditRoleForm from "@/Components/Project/Role/EditRoleForm.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { confirmAction } from "@/Composables/swal";

const props = defineProps({ show: Boolean, project: Object });
const emit = defineEmits(["close", "data-changed"]);

const form = useForm({});
const searchQuery = ref("");
const showAddRoleForm = ref(false);
const showEditRoleForm = ref(false);
const selectedRole = ref(null);

const filteredRoles = computed(() => {
    if (!props.project?.project_roles) return [];
    if (!searchQuery.value) return props.project.project_roles;
    return props.project.project_roles.filter(role => role.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const closeModal = () => emit("close");
const openEditModal = (role) => {
    selectedRole.value = role;
    showEditRoleForm.value = true;
};

const deleteRole = async (role) => {
    const result = await confirmAction('Hapus Role?', `Anda yakin ingin menghapus role "${role.name}"?`);
    if (result.isConfirmed) {
        form.delete(route("projects.roles.destroy", role.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => emit("data-changed"),
        });
    }
};
</script>

<template>
    <AddRoleForm v-if="project" :show="showAddRoleForm" :project="project" @close="showAddRoleForm = false" @role-added="emit('data-changed')" />
    <EditRoleForm v-if="project" :show="showEditRoleForm" :role="selectedRole" @close="showEditRoleForm = false" @role-updated="emit('data-changed')" />

    <Modal id="RoleList" :show="props.show" @close="closeModal">
        <div class="p-6">
            <template v-if="project">
                <div class="flex flex-row items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Daftar Role di {{ project.name }}</h2>
                    <PrimaryButton @click="showAddRoleForm = true">Tambah Role</PrimaryButton>
                </div>
                <TextInput v-model="searchQuery" type="text" class="block w-full" placeholder="Cari Role..." />
                <ul v-if="filteredRoles.length > 0" class="mt-6 divide-y divide-gray-200">
                    <li v-for="role in filteredRoles" :key="role.id" class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ role.name }}</p>
                            <p class="text-sm text-gray-500">{{ role.description }}</p>
                        </div>
                        <div class="space-x-2">
                            <PrimaryButton @click="openEditModal(role)" class="bg-yellow-600 hover:bg-yellow-700">Edit</PrimaryButton>
                            <PrimaryButton @click="deleteRole(role)" class="bg-red-600 hover:bg-red-700">Hapus</PrimaryButton>
                        </div>
                    </li>
                </ul>
                <p v-else class="mt-6 text-gray-500">Tidak ada role di project ini.</p>
            </template>
            <div class="mt-6 flex justify-end"> <SecondaryButton @click="closeModal">Tutup</SecondaryButton> </div>
        </div>
    </Modal>
</template>