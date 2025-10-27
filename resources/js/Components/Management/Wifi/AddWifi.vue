<script setup>
import { ref, nextTick } from "vue";
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
});

const emit = defineEmits(["close"]);

const form = useForm({
    name: null,
    ip: null,
});

const nameAddInput = ref(null);
const IpAddInput = ref(null);

const addWifi = () => {
    form.post(route("wifi.create"), {
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

const getIp = async () => {
    try {
        const response = await window.axios.get(route("wifi.get"));
        const ip = response.data.ip;

        IpAddInput.value = ip;
        form.ip = ip;
    } catch (error) {
        console.log(error);
        showError("Gagal","Gagal Mengambil Ip")
    }
};
</script>

<template>
    <Modal id="addWifi" :show="props.show" @close="closeAndReset">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Masukkan Wifi Baru!
            </h2>
            <div class="mt-6">
                <TextInput
                    id="name"
                    ref="nameAddInput"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Nama Wifi"
                    @keyup.enter="addWifi"
                    required
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mt-6">
                <TextInput
                    id="ip"
                    ref="IpAddInput"
                    v-model="form.ip"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="Ip Wifi"
                    @keyup.enter="addWifi"
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
                    @click="addWifi"
                >
                    Add
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
