<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
</script>

<template>
    <div class="h-screen">
        <NavbarLayout />
        <GuestLayout>
            <Head title="Register" />
            <div class="flex flex-col gap-4">
                <h2 class="inline-flex text-lg font-semibold items-center justify-center">Choose Your Role</h2>
                <Link
                    :href="route('register.admin.get')"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Register As Admin
                </Link>
                <Link
                    :href="route('register.employee.get')"
                    class="inline-flex items-center justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Register As Employee
                </Link>
            </div>
        </GuestLayout>
    </div>
</template>
