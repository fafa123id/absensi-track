<script setup>
import { ref, computed } from "vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import { confirmAction, showSuccess } from "@/Composables/swal";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({ projects: Array, company: Object });
const emit = defineEmits(["open-roles"]);

const search = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(5);
const form = useForm({});

const filteredProjects = computed(() => {
    if (!search.value) return props.projects;
    return props.projects.filter((p) =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
    );
});
const paginatedProjects = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    return filteredProjects.value.slice(start, start + itemsPerPage.value);
});
const totalPages = computed(() =>
    Math.ceil(filteredProjects.value.length / itemsPerPage.value)
);

const deleteProject = async (id, name) => {
    const result = await confirmAction(
        "Hapus Proyek?",
        `Anda yakin ingin menghapus proyek "${name}"?`
    );
    if (result.isConfirmed) {
        form.delete(route("projects.destroy", id), {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

const refreshToken = async (id, name) => {
    const result = await confirmAction(
        "Segarkan Token?",
        `Token baru akan dibuat untuk proyek "${name}".`
    );
    if (result.isConfirmed) {
        form.patch(route("projects.refreshToken", id), {
            preserveState: true,
            preserveScroll: true,
        });
    }
};
const visibleTokenId = ref(null);
const copiedTokenId = ref(null);

const toggleVisibility = (projectId) => {
    visibleTokenId.value =
        visibleTokenId.value === projectId ? null : projectId;
};

const copyToken = async (token, projectId) => {
    try {
        await navigator.clipboard.writeText(token);
        await showSuccess(
            "Berhasil Menyalin Token!",
            "Token telah disalin ke clipboard."
        );
        copiedTokenId.value = projectId;
        setTimeout(() => {
            copiedTokenId.value = null;
        }, 2000);
    } catch (err) {
        console.error("Gagal menyalin token: ", err);
    }
};
const formatDateRange = (project) => {
    const start = project.start_date;
    const end = project.end_date;

    if (start && end) {
        return `${start} - ${end}`;
    }
    if (start) {
        return `Mulai: ${start}`;
    }
    if (end) {
        return `Selesai: ${end}`;
    }
    return "Belum diatur";
};
</script>

<template>
    <div
        class="bg-white overflow-x-auto shadow-sm sm:rounded-lg p-8 flex flex-col"
    >
        <h1 class="text-2xl font-bold mb-4 text-center">
            Daftar Proyek
            <PrimaryButton
                :href="route('projects.create')"
                as="a"
                class="bg-green-600 hover:bg-green-700 ml-4"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
            </PrimaryButton>
        </h1>
        <TextInput
            v-model="search"
            type="text"
            class="w-full md:w-1/2 self-center mb-8"
            placeholder="Cari Proyek..."
        />
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Proyek</th>
                    <th class="px-6 py-3 text-left">Token Proyek</th>
                    <th class="px-6 py-3 text-left">Deskripsi</th>
                    <th class="px-6 py-3 text-left">Scope Proyek</th>
                    <th class="px-6 py-3 text-left">Periode Proyek</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="min-w-full divide-y divide-gray-200">
                <tr v-for="project in paginatedProjects" :key="project.id">
                    <td class="px-6 py-4">{{ project.name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <input
                                :type="
                                    visibleTokenId === project.id
                                        ? 'text'
                                        : 'password'
                                "
                                :value="project.token"
                                readonly
                                class="flex-grow p-1 border rounded bg-gray-100 focus:outline-none"
                            />
                            <button
                                @click="toggleVisibility(project.id)"
                                class="text-gray-500 hover:text-gray-800"
                            >
                                <svg
                                    v-if="visibleTokenId === project.id"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.243 4.243L6.228 6.228"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="copyToken(project.token, project.id)"
                                class="text-gray-500 hover:text-gray-800"
                            >
                                <svg
                                    v-if="copiedTokenId === project.id"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5 text-green-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m4.5 12.75 6 6 9-13.5"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-5 h-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"
                                    />
                                </svg>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ project.description }}</td>
                    <td class="px-6 py-4">{{ project.scope }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ formatDateRange(project) }}
                    </td>
                    <td class="flex flex-row px-6 py-4 gap-2">
                        <PrimaryButton
                            @click="$emit('open-roles', project)"
                            class="bg-yellow-600 hover:bg-yellow-700"
                            >Roles</PrimaryButton
                        >
                        <PrimaryButton
                            :href="route('projects.edit', project.id)"
                            class="bg-blue-600 hover:bg-blue-700"
                            >Edit</PrimaryButton
                        >
                        <PrimaryButton
                            @click="deleteProject(project.id, project.name)"
                            class="bg-red-600 hover:bg-red-700"
                            >Hapus</PrimaryButton
                        >
                        <PrimaryButton
                            @click="refreshToken(project.id, project.name)"
                            class="bg-green-600 hover:bg-green-700"
                            >Refresh Token</PrimaryButton
                        >
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination
            :totalPages="totalPages"
            :currentPage="currentPage"
            @page-changed="currentPage = $event"
            class="mt-6"
        />
    </div>
</template>
