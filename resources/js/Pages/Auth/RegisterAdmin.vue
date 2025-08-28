<script setup>
import { ref, computed } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useVuelidate } from "@vuelidate/core";
import {
    required,
    email,
    minLength,
    numeric,
    helpers,
    sameAs,
} from "@vuelidate/validators";

const currentStep = ref(1);

const form = useForm({
    company_name: "",
    company_address: "",
    company_phone: "",
    company_email: "",
    user_name: "",
    email: "",
    password: "",
    password_confirmation: "",
});
const rules = computed(() => {
    const baseRules = {
        company_name: {
            required: helpers.withMessage(
                "Nama perusahaan harus diisi.",
                required
            ),
        },
        company_email: {
            required: helpers.withMessage(
                "Email perusahaan harus diisi.",
                required
            ),
            email: helpers.withMessage("Format email tidak valid.", email),
        },
        company_phone: {
            required: helpers.withMessage(
                "Nomor telepon harus diisi.",
                required
            ),
            numeric: helpers.withMessage(
                "Nomor telepon harus berupa angka.",
                numeric
            ),
        },
        company_address: {
            required: helpers.withMessage(
                "Alamat perusahaan harus diisi.",
                required
            ),
        },
    };

    
    if (currentStep.value === 2) {
        return {
            ...baseRules,
            user_name: {
                required: helpers.withMessage(
                    "Nama pengguna harus diisi.",
                    required
                ),
            },
            email: {
                required: helpers.withMessage("Email harus diisi.", required),
                email: helpers.withMessage("Format email tidak valid.", email),
            },
            password: {
                required: helpers.withMessage(
                    "Password harus diisi.",
                    required
                ),
                minLength: helpers.withMessage(
                    "Password minimal 8 karakter.",
                    minLength(8)
                ),
            },
            password_confirmation: {
                required: helpers.withMessage(
                    "Konfirmasi password harus diisi.",
                    required
                ),
                sameAs: helpers.withMessage(
                    "Konfirmasi password tidak cocok.",
                    sameAs(form.password)
                ),
            },
        };
    }

    return baseRules;
});
const v$ = useVuelidate(rules, form);

const nextStep = async () => {
    const isValid = await v$.value.$validate();
    if (isValid) {
        currentStep.value++;
    }
};

const prevStep = () => {
    currentStep.value--;
};

const submit = async () => {
    const isValid = await v$.value.$validate();
    if (!isValid) return;

    form.post(route("register.admin.post"), {
        onFinish: () => {
            currentStep.value = 1;
            form.reset("password", "password_confirmation");
        },
    });
};
const fieldErrors = computed(() => {
    const baseErrors = {
        company_name: v$.value.company_name.$errors[0]?.$message || form.errors.company_name,
        company_email: v$.value.company_email.$errors[0]?.$message || form.errors.company_email,
        company_phone: v$.value.company_phone.$errors[0]?.$message || form.errors.company_phone,
        company_address: v$.value.company_address.$errors[0]?.$message || form.errors.company_address,
    };

    if (currentStep.value === 2) {
        return {
            ...baseErrors,
            user_name: v$.value.user_name?.$errors[0]?.$message || form.errors.user_name,
            email: v$.value.email?.$errors[0]?.$message || form.errors.email,
            password: v$.value.password?.$errors[0]?.$message || form.errors.password,
            password_confirmation: v$.value.password_confirmation?.$errors[0]?.$message || form.errors.password_confirmation,
        };
    }
    return baseErrors;
});
</script>

<template>
    <div class="h-screen">
        <NavbarLayout />
        <!-- <pre class="mt-6 p-4 bg-gray-100 rounded text-xs whitespace-pre-wrap">{{
            form
        }}</pre> -->
        <GuestLayout>
            <Head title="Register Company" />
            <form @submit.prevent="submit">
                <div v-if="currentStep === 1">
                    <h2 class="text-lg font-medium text-gray-900">
                        Langkah 1: Informasi Perusahaan
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Mari mulai dengan data perusahaan Anda.
                    </p>

                    <div class="mt-4">
                        <InputLabel
                            for="company_name"
                            value="Nama Perusahaan"
                        />
                        <TextInput
                            id="company_name"
                            type="text"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': v$.company_name.$error,
                            }"
                            v-model="form.company_name"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.company_name"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel
                            for="company_email"
                            value="Email Perusahaan"
                        />
                        <TextInput
                            id="company_email"
                            type="email"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': v$.company_email.$error,
                            }"
                            v-model="form.company_email"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.company_email"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel
                            for="company_phone"
                            value="Telepon Perusahaan"
                        />
                        <TextInput
                            id="company_phone"
                            type="tel"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': v$.company_phone.$error,
                            }"
                            v-model="form.company_phone"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.company_phone"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel
                            for="company_address"
                            value="Alamat Perusahaan"
                        />
                        <TextInput
                            id="company_address"
                            type="text"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': v$.company_address.$error,
                            }"
                            v-model="form.company_address"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.company_address"
                        />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton type="button" @click="nextStep">
                            Selanjutnya &rarr;
                        </PrimaryButton>
                    </div>
                </div>

                <div v-if="currentStep === 2">
                    <h2 class="text-lg font-medium text-gray-900">
                        Langkah 2: Akun Admin Anda
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Sekarang, buat akun admin utama untuk perusahaan ini.
                    </p>

                    <div class="mt-4">
                        <InputLabel for="user_name" value="Nama Anda" />
                        <TextInput
                            id="user_name"
                            type="text"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': v$.user_name.$error,
                            }"
                            v-model="form.user_name"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.user_name"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': v$.email.$error,
                            }"
                            v-model="form.email"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.email"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500': v$.password.$error,
                            }"
                            v-model="form.password"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.password"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel
                            for="password_confirmation"
                            value="Konfirmasi Password"
                        />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            :class="{
                                'border-red-500':
                                    v$.password_confirmation.$error,
                            }"
                            v-model="form.password_confirmation"
                        />
                        <InputError
                            class="mt-2"
                            :message="fieldErrors.password_confirmation"
                        />
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <button
                            type="button"
                            @click="prevStep"
                            class="text-sm text-gray-600 hover:text-gray-900 underline"
                        >
                            &larr; Kembali
                        </button>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Daftarkan Perusahaan
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </GuestLayout>
    </div>
</template>
