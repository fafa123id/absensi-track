<script setup>
import { ref, nextTick } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { confirmAction } from "@/Composables/swal";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    department: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const nameAddInput = ref(null);
const emailAddInput = ref(null);
const passwordAddInput = ref(null);

const addEmployee = async () => {
    form.clearErrors();
    const result = await confirmAction(
        "Yakin untuk menambahkan Karyawan ?",
        "Karyawan baru akan ditambahkan ke departemen " + props.department.name,
        "info"
    );

    if (result?.isConfirmed) {
        form.post(route("employees.store", props.department.id), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            },
        });
    }
};
</script>

<template>
    <Head title="Add Employee" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 p-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8"
                >
                    <h2 class="text-lg font-medium text-gray-900">
                        Masukkan Karyawan Baru Untuk Departemen
                        {{ props.department.name }}!
                    </h2>
                    <form @submit.prevent="addEmployee">
                        <div class="mt-6">
                            <TextInput
                                id="name"
                                ref="nameAddInput"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Nama Karyawan"
                                @keyup.enter="addEmployee"
                                required
                            />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2"
                            />
                        </div>
                        <div class="mt-6">
                            <TextInput
                                id="email"
                                ref="emailAddInput"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                placeholder="Email Karyawan"
                                @keyup.enter="addEmployee"
                                required
                            />
                            <InputError
                                :message="form.errors.email"
                                class="mt-2"
                            />
                        </div>
                        <div class="mt-6">
                            <TextInput
                                id="password"
                                ref="passwordAddInput"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="Password Karyawan"
                                @keyup.enter="addEmployee"
                                required
                            />
                            <InputError
                                :message="form.errors.password"
                                class="mt-2"
                            />
                        </div>
                                                <div class="mt-6">
                            <TextInput
                                id="password"
                                ref="passwordAddInput"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="Password Konfirmasi Karyawan"
                                @keyup.enter="addEmployee"
                                required
                            />
                            <InputError
                                :message="form.errors.password_confirmation"
                                class="mt-2"
                            />
                        </div>
                        <div class="mt-6 flex justify-between">
                            <PrimaryButton :href="route('dashboard')">
                                Kembali
                            </PrimaryButton>
                            <PrimaryButton
                                class="ms-3"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                :type="'submit'"
                            >
                                Add
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
