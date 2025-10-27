<script setup>
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({ show: Boolean, role: Object });
const emit = defineEmits(['close', 'role-updated']);
const form = useForm({ name: '', description: '' });

watch(() => props.show, (isVisible) => {
    if (isVisible && props.role) {
        form.name = props.role.name;
        form.description = props.role.description;
        form.clearErrors();
    }
});

const submit = () => {
    if (!props.role) return;
    form.put(route('projects.roles.update', props.role.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            emit('role-updated');
        }
    });
};

const closeModal = () => emit('close');
</script>

<template>
    <Modal id="editRole" :show="props.show" @close="closeModal">
        <form @submit.prevent="submit" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Edit Role "{{ role?.name }}"</h2>
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
                <PrimaryButton class="ms-3" :disabled="form.processing">Simpan Perubahan</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>