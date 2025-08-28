<script setup>
import NavbarLayout from '@/Layouts/NavbarLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
const page = usePage();
import { confirmAction, showSuccess, showError } from "@/Composables/swal";

watch(() => page.props.flash.success, (newMessage) => {
    if (newMessage) {
        showSuccess('Berhasil!', newMessage);
        page.props.flash.success = null;
    }
});
watch(() => page.props.flash.error, (newMessage) => {
    if (newMessage) {
        showError('Gagal!', newMessage);
        page.props.flash.error = null;
    }
});

</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <NavbarLayout />
            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
