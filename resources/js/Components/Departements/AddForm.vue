<script setup>
import { ref, nextTick } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: null,
});

const nameAddInput = ref(null);

const addDepartment = () => {
    form.post(route("departements.store"), {
        preserveScroll: true,
        onSuccess: () => closeAndReset(),
        onError: () => nameAddInput.value.focus(),
    });
};

const closeAndReset = () => {
    emit("close");
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Modal :show="props.show" @close="closeAndReset">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Masukkan Departemen Baru!
            </h2>
            <div class="mt-6">
                <TextInput
                    id="name"
                    ref="nameAddInput"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Nama Departemen"
                    @keyup.enter="addDepartment"
                    required
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeAndReset">
                    Cancel
                </SecondaryButton>
                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="addDepartment"
                >
                    Add
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
