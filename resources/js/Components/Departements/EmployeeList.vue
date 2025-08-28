<script setup>
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    department: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["close"]);
</script>

<template>
    <Modal :show="props.show" @close="$emit('close')">
        <div class="p-6">
            <template v-if="department">
                <div class="flex flex-row items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">
                        Daftar Karyawan di Departemen {{ department.name }}
                    </h2>
                    <PrimaryButton :href="route('dashboard')">
                        Tambah Karyawan
                    </PrimaryButton>
                </div>

                <div class="mt-6">
                    <ul
                        v-if="department.users && department.users.length > 0"
                        class="divide-y divide-gray-200"
                    >
                        <li
                            v-for="employee in department.users"
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
                                <PrimaryButton
                                    :href="route('welcome')"
                                    class="bg-blue-600 hover:bg-blue-700"
                                >
                                    Edit
                                </PrimaryButton>
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-gray-500">
                        Tidak ada karyawan di departemen ini.
                    </p>
                </div>
            </template>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')">
                    Tutup
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
