<script setup>
import { ref, nextTick, watch } from "vue";
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
    department: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: null,
});

const EditDepartment = () => {
    form.put(route("departements.update", props.department.id), {
        preserveScroll: true,
        onSuccess: () => closeAndReset(),
        onError: () => nameEditInput.value.focus(),
    });
};
watch(() => props.department, (newDepartment) => {
    if (newDepartment) {
        form.name = newDepartment.name;
    }
});
const nameEditInput = ref(null);

const closeAndReset = () => {
    emit("close");
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Modal id="DepartementEdit" :show="props.show" @close="closeAndReset">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Edit Departemen!
            </h2>
            <div class="mt-6">
                <TextInput
                    :value="form.name"
                    id="name"
                    ref="nameEditInput"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Nama Departemen"
                    @keyup.enter="EditDepartment"
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
                    @click="EditDepartment"
                >
                    Edit
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
