<script setup>
import { ref, computed, watch } from "vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import { showSuccess, confirmAction } from "@/Composables/swal";
import { useForm } from "@inertiajs/vue3";
import AddWifi from "./AddWifi.vue";
import EditWifi from "./EditWifi.vue";

const props = defineProps({
    Wifis: Array,
});

const emit = defineEmits(["open-add", "open-edit"]);

const search = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(5);

const filteredWifis = computed(() => {
    currentPage.value = 1;
    if (!search.value) {
        return props.Wifis;
    }
    return props.Wifis.filter((wifi) =>
        wifi.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const paginatedWifis = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredWifis.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredWifis.value.length / itemsPerPage.value);
});

const form = useForm({});

const showAddWifi = ref(false);
const showEditWifi = ref(false);
const selectedWifi = ref(null);
const editWifi = (wifi) => {
    selectedWifi.value = wifi;
    showEditWifi.value = true
}
const deleteWifi = async (id, name) => {
    const result = await confirmAction(
        "Hapus Wifi " + name + "?",
        "Wifi tidak akan bisa digunakan lagi untuk request",
        "info"
    );

    if (result.isConfirmed) form.delete(route("wifi.destroy", id));
};
</script>

<template>
    <AddWifi :show="showAddWifi" @close="showAddWifi = false" />
    <EditWifi :wifi="selectedWifi" :show="showEditWifi" @close="showEditWifi = false" />
    <div
        class="bg-white overflow-x-auto shadow-sm sm:rounded-lg p-8 flex flex-col"
    >
        <h1 class="text-2xl font-bold mb-4 text-center">
            Daftar Wifi Company
            <PrimaryButton
                @click="showAddWifi = true"
                class="bg-green-600 hover:bg-green-700"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
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
            placeholder="Cari Wifi"
        />

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Wifi</th>
                    <th class="px-6 py-3 text-left">Ip Wifi</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="Wifi in paginatedWifis" :key="Wifi.id">
                    <td class="px-6 py-4">{{ Wifi.name }}</td>
                    <td class="px-6 py-4">
                        {{ Wifi.ip }}
                    </td>
                    <td class="flex flex-row px-6 py-4 gap-2">
                        <PrimaryButton
                            @click="editWifi(Wifi)"
                            class="bg-blue-600 hover:bg-blue-700"
                            >Edit</PrimaryButton
                        >
                        <PrimaryButton
                            @click="deleteWifi(Wifi.id, Wifi.name)"
                            class="bg-red-600 hover:bg-red-700"
                            >Hapus</PrimaryButton
                        >
                    </td>
                </tr>
                <tr v-if="paginatedWifis.length === 0">
                    <td colspan="4" class="text-center py-4 text-gray-500">
                        Tidak ada departemen yang cocok dengan pencarian Anda.
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
