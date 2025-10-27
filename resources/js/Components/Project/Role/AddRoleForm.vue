<script setup>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({ show: Boolean, project: Object });
const emit = defineEmits(['close', 'role-added']);
const form = useForm({ name: '', description: '' });

const submit = () => {
    form.post(route('projects.roles.store', props.project.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
            emit('role-added');
        }
    });
};

const closeModal = () => { form.reset(); emit('close'); };
</script>

<template>
    <Modal id="AddRole" :show="props.show" @close="closeModal">
        <form @submit.prevent="submit" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Tambah Role Baru ke {{ project.name }}</h2>
            <div class="mt-6">
                <TextInput v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Nama Role" required />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mt-6">
                <TextInput v-model="form.description" type="text" class="mt-1 block w-full" placeholder="Deskripsi Role" />
                <InputError :message="form.errors.description" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton type="button" @click="closeModal">Batal</SecondaryButton>
                <PrimaryButton class="ms-3" :disabled="form.processing">Simpan</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>