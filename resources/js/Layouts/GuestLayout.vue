<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link, router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted, watch } from "vue";
const page = usePage();
import { confirmAction, showSuccess, showError } from "@/Composables/swal";
import { setupAuthCheck } from "@/Composables/useAuthCheck";
import { setupSwalFlashMessages } from "@/Composables/useSwal";

setupSwalFlashMessages();
setupAuthCheck();
</script>

<template>
    <div
        class="flex h-full pb-28 flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
