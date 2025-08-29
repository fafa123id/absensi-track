<script setup>
import { ref, watch } from "vue"; // Impor 'watch'
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue"; // Anda tidak menggunakan ini, bisa dihapus
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    employee: {
        type: Object,
        default: null,
    },
    departements: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    departement_id: "",
});

watch(
    () => props.employee,
    (newEmployee) => {
        if (newEmployee) {
            form.departement_id = newEmployee.departement_id;
        } else {
            form.reset();
        }
    }
);

const editEmployee = () => {
    console.log("Mutating employee to department ID:", form.departement_id);
    console.log("employee", props.employee.id);
    console.log('departements', props.departements)
    form.put(route("employees.update", props.employee.id), {
        preserveScroll: true,
        onSuccess: () => closeAndReset(),
    });
};

const closeAndReset = () => {
    emit("close");
    form.reset();
};
</script>

<template>
    <Modal :show="props.show" @close="closeAndReset">
        <div class="p-6">
            <form @submit.prevent="editEmployee">
                <h2 class="text-lg font-medium text-gray-900">
                    Mutasi Karyawan
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Pindahkan karyawan "{{ props.employee?.name }}" ke
                    departemen lain.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="departement_id"
                        value="Pilih Departemen Baru"
                    />
                    <select
                        id="departement_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        v-model="form.departement_id"
                        required
                    >
                        <option disabled value="">
                            -- Pilih Departemen --
                        </option>
                        <option
                            v-for="department in props.departements"
                            :key="department.id"
                            :value="department.id"
                        >
                            {{ department.name }}
                        </option>
                    </select>
                    <InputError
                        :message="form.errors.departement_id"
                        class="mt-2"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton type="button" @click="closeAndReset"
                        >Batal</SecondaryButton
                    >
                    <PrimaryButton
                        type="submit"
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Simpan Perubahan
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
