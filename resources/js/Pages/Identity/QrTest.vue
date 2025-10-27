<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import VueQrcode from "@chenfengyuan/vue-qrcode";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CountdownTimer from "@/Components/CountdownTimer.vue"; // <-- Impor komponen timer
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";

const qrSession = ref(null);
const status = ref("loading"); // loading, active, expired, success
const page = usePage();
const generateQr = async () => {
    status.value = "loading";

    try {
        const response = await fetch(route("identity.qr.get"), {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        });
        if (!response.ok) throw new Error("Gagal membuat sesi QR.");

        qrSession.value = await response.json();
        status.value = "active";
        console.log("QR Session:", qrSession.value);
    } catch (error) {
        console.error("Error saat generate QR:", error);
        status.value = "expired";
    }
};


// Panggil generateQr saat komponen pertama kali dimuat
onMounted(() => {
    generateQr();
});

</script>

<template>
    <Head title="Qr Login" />
    <div class="h-screen">
        <NavbarLayout />
        <GuestLayout>
            <div class="flex flex-col items-center justify-center p-6">
                <h2 class="text-2xl font-bold mb-2">Identity QR Code</h2>
                <p class="text-gray-600 mb-4">
                    Pindai kode ini dengan aplikasi mobile Anda.
                </p>

                <div
                    class="w-[200px] h-[200px] bg-gray-100 flex items-center justify-center rounded-lg shadow-inner"
                >
                    <div v-if="status === 'loading'" class="text-gray-500">
                        Memuat QR Code...
                    </div>

                    <vue-qrcode
                        v-if="status === 'active' && qrSession"
                        :value="route('identity.qr',qrSession.identity)"
                        :options="{ width: 200 }"
                    ></vue-qrcode>

                    <div v-if="status === 'success'" class="text-center p-4">
                        <p class="font-semibold text-green-600">
                            Login Berhasil!
                        </p>
                        <p class="text-gray-500">Mengarahkan ke dashboard...</p>
                    </div>
                </div>
            </div>
        </GuestLayout>
    </div>
</template>
