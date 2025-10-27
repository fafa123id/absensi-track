<script setup>
import { ref, nextTick, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import { showError } from "@/Composables/swal";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    wifi: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close"]);
const form = useForm({
    name: null,
    ip: null,
});
watch(() => props.show, (isVisible) => {
    if (isVisible && props.wifi) {
        form.name = props.wifi.name;
        form.ip = props.wifi.ip;
        form.clearErrors();
    }
});
const nameEditInput = ref(null);
const IpEditInput = ref(null);

const editWifi = () => {
    form.put(route("wifi.update", props.wifi.id), {
        preserveScroll: true,
        onSuccess: () => closeAndReset(),
        onError: () => nameEditInput.value.focus(),
    });
};

const closeAndReset = () => {
    emit("close");
    form.clearErrors();
    form.reset();
};

const getIp = async () => {
    try {
        const response = await window.axios.get(route("wifi.get"));
        const ip = response.data.ip;

        IpEditInput.value = ip;
        form.ip = ip;
    } catch (error) {
        console.log(error);
        showError("Gagal", "Gagal Mengambil Ip");
    }
};
</script>

<template>
    <Modal id="editWifi" :show="props.show" @close="closeAndReset">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Edit Wifi {{ props.wifi.name }}
            </h2>
            <div class="mt-6">
                <TextInput
                    id="name"
                    ref="nameEditInput"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Nama Wifi"
                    @keyup.enter="editWifi"
                    required
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mt-6">
                <TextInput
                    id="ip"
                    ref="IpEditInput"
                    v-model="form.ip"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Ip Wifi"
                    @keyup.enter="editWifi"
                    required
                />
                <InputError :message="form.errors.ip" class="mt-2" />
                <PrimaryButton class="mt-2" @click="getIp">
                    Fetch From Current Wifi
                </PrimaryButton>
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeAndReset">
                    Cancel
                </SecondaryButton>
                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="editWifi"
                >
                    Edit
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
