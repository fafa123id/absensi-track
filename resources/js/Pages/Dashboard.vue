<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
defineProps({
    employees: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama</th>
                                <th class="px-6 py-3 text-left">Email</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="employee in employees.data"
                                :key="employee.id"
                            >
                                <td class="px-6 py-4">{{ employee.name }}</td>
                                <td class="px-6 py-4">{{ employee.email }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 p-4">
                        <Pagination :links="employees.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
