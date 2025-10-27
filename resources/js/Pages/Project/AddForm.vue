<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    name: '',
    description: '',
    scope: '',
    start_date: '',
    end_date: '',
});

const submit = () => {
    form.post(route('projects.store'));
};
</script>

<template>
    <Head title="Tambah Proyek Baru" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Proyek Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Nama Proyek" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi" />
                            <textarea id="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.description" rows="3"></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>
                        
                        <div>
                            <InputLabel for="scope" value="Scope / Target Global" />
                            <textarea id="scope" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.scope" rows="3"></textarea>
                            <InputError class="mt-2" :message="form.errors.scope" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="start_date" value="Tanggal Mulai" />
                                <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>
                            <div>
                                <InputLabel for="end_date" value="Tanggal Selesai" />
                                <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" />
                                <InputError class="mt-2" :message="form.errors.end_date" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing" :type="'submit'">Simpan Proyek</PrimaryButton>
                            <Link :href="route('dashboard')" class="text-sm text-gray-600 hover:text-gray-900">Batal</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>