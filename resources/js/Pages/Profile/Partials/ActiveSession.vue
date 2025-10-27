<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";

const confirmingSessionDeletion = ref(false);
const selectedSession = ref(null);
const form = useForm({});

const confirmSessionDeletion = (session) => {
    confirmingSessionDeletion.value = true;
    selectedSession.value = session;
};

const deleteSession = () => {
    form.delete(route("profile.session.destroy", selectedSession.value), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingSessionDeletion.value = false;
    form.clearErrors();
    form.reset();
};
const page = usePage();
const user = page.props.auth.user;
window.Echo.private(`App.User.${user.id}`).listen("SessionLoggedOut", (e) => {
    fetchData();
});
window.Echo.private(`App.User.${user.id}`).listen("SessionLoggedIn", (e) => {
    fetchData();
});
const fetchData = () => {
    router.reload({ only: ["session", "thisSession"] });
};
</script>

<template>
    <section class="space-y-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <header class="text-left">
            <h2 class="text-lg font-medium text-gray-900">
                Here are your active sessions
                <PrimaryButton class="ms-3" @click="fetchData"
                    >Fetch</PrimaryButton
                >
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                You may log out from all of your other browser sessions across
                all of your devices. If you feel your account has been
                compromised, you should also update your password.
            </p>
        </header>

        <div class="bg-white shadow sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-900">
                    <thead class="hidden md:table-header-group bg-gray-50">
                        <tr>
                            <th class="p-4 text-left font-semibold">
                                IP Address
                            </th>
                            <th class="p-4 text-left font-semibold">Agent</th>
                            <th class="p-4 text-left font-semibold">
                                Last Active
                            </th>
                            <th class="p-4 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="session in $page.props.session"
                            :key="session.id"
                            :class="{
                                'bg-green-50':
                                    session.id === $page.props.thisSession,
                            }"
                            class="block md:table-row mb-4 md:mb-0"
                        >
                            <td
                                data-label="IP Address"
                                class="p-4 flex justify-between items-center md:table-cell border-b md:border-none"
                            >
                                <span class="font-bold md:hidden"
                                    >IP Address</span
                                >
                                <span>{{ session.ip_address }}</span>
                            </td>
                            <td
                                data-label="Agent"
                                class="p-4 flex justify-between md:table-cell border-b md:border-none"
                            >
                                <span class="font-bold md:hidden">Agent</span>
                                <div class="text-right md:text-left">
                                    {{ session.user_agent }}
                                </div>
                            </td>
                            <td
                                data-label="Last Active"
                                class="p-4 flex justify-between items-center md:table-cell border-b md:border-none"
                            >
                                <span class="font-bold md:hidden"
                                    >Last Active</span
                                >
                                <span>{{ session.last_active }}</span>
                            </td>
                            <td
                                data-label="Action"
                                class="p-4 flex justify-end items-center md:table-cell"
                            >
                                <DangerButton
                                    @click="confirmSessionDeletion(session.id)"
                                    >Delete</DangerButton
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Modal :show="confirmingSessionDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete this session?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Once a session is deleted, you will be logged out from that
                    device. Please enter your password to confirm you would like
                    to log out of this browser session.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"
                        >Cancel</SecondaryButton
                    >
                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteSession"
                    >
                        Delete Session
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
