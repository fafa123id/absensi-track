<script setup>
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { confirmAction } from "@/Composables/swal";
import { ref, computed } from "vue";
import EditEmployeeModal from "@/Components/Departements/EditEmployeeModal.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    department: {
        type: Object,
        default: null,
    },
    departements: {
        type: Array,
        default: () => [],
    },
});
const form = useForm({});

const ConfirmDelete = ref(false);
const selectedEmployeeId = ref(null);
const selectedEmployee = ref(null);

const deleteEmployeeModal = (employeeId) => {
    selectedEmployeeId.value = employeeId;
    ConfirmDelete.value = true;
};
const editEmployee = ref(false);

const openEditEmployeeModal = (employee) => {
    editEmployee.value = true;
    selectedEmployee.value = employee;
};
const closeEditEmployeeModal = () => {
    editEmployee.value = false;
    searchQuery.value = "";
};
const emit = defineEmits(["close"]);

const deleteEmployee = (employeeId) => {
    form.delete(route("employees.destroy", employeeId), {
        preserveScroll: true,
        onFinish: () => {
            ConfirmDelete.value = false;
            closeModal();
        },
        onError: () => {
            ConfirmDelete.value = false;
        },
    });
};
const searchQuery = ref("");

const filteredEmployees = computed(() => {
    if (!props.department || !props.department.users) {
        return [];
    }

    if (!searchQuery.value) {
        return props.department.users;
    }

    const lowerCaseQuery = searchQuery.value.toLowerCase();
    return props.department.users.filter(
        (employee) =>
            employee.name.toLowerCase().includes(lowerCaseQuery) ||
            employee.email.toLowerCase().includes(lowerCaseQuery)
    );
});

const closeModal = () => {
    emit("close");
    searchQuery.value = "";
};

const closeDeleteEmployeeModal = () => {
    ConfirmDelete.value = false;
    searchQuery.value = "";
};
</script>

<template>
    <EditEmployeeModal
        :show="editEmployee"
        :employee="selectedEmployee"
        :departements="props.departements"
        @close="closeEditEmployeeModal"
        @update-successful="closeModal"
    />
    <Modal :show="ConfirmDelete" @close="closeDeleteEmployeeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Hapus Karyawan</h2>
            <p class="mt-2 text-sm text-gray-600">
                Apakah Anda yakin ingin menghapus karyawan ini?
            </p>
            <div class="mt-6 flex justify-end space-x-2">
                <SecondaryButton @click="closeDeleteEmployeeModal">
                    Batal
                </SecondaryButton>
                <PrimaryButton
                    @click="deleteEmployee(selectedEmployeeId)"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Hapus
                </PrimaryButton>
            </div>
        </div>
    </Modal>
    <Modal :show="props.show" @close="closeModal">
        <div class="p-6">
            <template v-if="department">
                <div class="flex flex-row items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">
                        Daftar Karyawan di Departemen {{ department.name }}
                    </h2>
                    <PrimaryButton
                        :href="route('employees.create', department.id)"
                    >
                        Tambah Karyawan
                    </PrimaryButton>
                </div>
                <div class="mt-4">
                    <TextInput
                        v-model="searchQuery"
                        type="text"
                        class="block w-full"
                        placeholder="Cari karyawan di departemen ini..."
                    />
                </div>

                <div class="mt-6">
                    <ul
                        v-if="filteredEmployees && filteredEmployees.length > 0"
                        class="divide-y divide-gray-200"
                    >
                        <li
                            v-for="employee in filteredEmployees"
                            :key="employee.id"
                            class="py-3"
                        >
                            <div
                                class="flex flex-row items-center justify-between"
                            >
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ employee.email }}
                                    </p>
                                </div>
                                <div class="space-x-2">
                                    <PrimaryButton
                                        @click="openEditEmployeeModal(employee)"
                                        class="bg-yellow-600 hover:bg-yellow-700"
                                    >
                                        Mutasikan
                                    </PrimaryButton>
                                    <PrimaryButton
                                        @click="
                                            deleteEmployeeModal(employee.id)
                                        "
                                        class="bg-red-600 hover:bg-red-700"
                                    >
                                        Hapus
                                    </PrimaryButton>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-gray-500">
                        Tidak ada karyawan di departemen ini.
                    </p>
                </div>
            </template>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal">
                    Tutup
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
