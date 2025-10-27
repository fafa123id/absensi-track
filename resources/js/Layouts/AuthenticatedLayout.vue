<script setup>
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { setupAuthCheck } from "@/Composables/useAuthCheck";
import { setupSwalFlashMessages } from "@/Composables/useSwal";
import { setupPrivateAuthWebsocket } from "@/Composables/useWebsocket";
import { setupLoginNotification } from "@/Composables/useLoginNotification";


setupPrivateAuthWebsocket();
setupAuthCheck();
setupSwalFlashMessages();
setupLoginNotification();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <NavbarLayout />
            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
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
