<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import VueQrcode from "@chenfengyuan/vue-qrcode";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CountdownTimer from "@/Components/CountdownTimer.vue"; // <-- Impor komponen timer
import GuestLayout from "@/Layouts/GuestLayout.vue";
import NavbarLayout from "@/Layouts/NavbarLayout.vue";

const qrSession = ref(null);
const status = ref("loading"); // loading, active, expired, success

const generateQr = async () => {
    status.value = "loading";

    // Hentikan listener lama jika ada, untuk mencegah duplikasi
    if (qrSession.value) {
        window.Echo.leave(`qr-login.${qrSession.value.id}`);
    }

    try {
        const response = await fetch(route("qr.make"));
        if (!response.ok) throw new Error("Gagal membuat sesi QR.");

        qrSession.value = await response.json();
        status.value = "active";
        console.log("QR Session:", qrSession.value);
        // Mulai mendengarkan event 'scanned' dari server
        listenForScan();
    } catch (error) {
        console.error("Error saat generate QR:", error);
        status.value = "expired";
    }
};

const listenForScan = () => {
    if (!qrSession.value) return;
    console.log("Mendengarkan channel:", `qr-login.${qrSession.value.id}`);
    window.Echo.channel(`qr-login.${qrSession.value.id}`).listen(
        "QrSessionScanned",
        (event) => {
            status.value = "success";
            console.log("QR Code telah dipindai dan diverifikasi:");
            router.visit(route("qr.status", { id: qrSession.value.id }), {
                onSuccess: () => {
                    router.reload();
                    window.Echo.leave(`qr-login.${qrSession.value.id}`);
                },
            });
        }
    );
};

// Fungsi ini akan dipanggil oleh komponen CountdownTimer saat waktu habis
const handleTimerFinished = () => {
    // Hanya ubah status jika belum 'success'
    if (status.value === "active") {
        status.value = "expired";
        // Berhenti mendengarkan karena sesi sudah tidak valid
        if (qrSession.value) {
            window.Echo.leave(`qr-login.${qrSession.value.id}`);
        }
    }
};

// Panggil generateQr saat komponen pertama kali dimuat
onMounted(() => {
    generateQr();
});

// Pastikan kita berhenti mendengarkan saat meninggalkan halaman
onUnmounted(() => {
    if (qrSession.value) {
        window.Echo.leave(`qr-login.${qrSession.value.id}`);
    }
});
</script>

<template>
    <Head title="Qr Login" />
    <div class="h-screen">
        <NavbarLayout />
        <GuestLayout>
            <div class="flex flex-col items-center justify-center p-6">
                <h2 class="text-2xl font-bold mb-2">Login dengan QR Code</h2>
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
                        :value="qrSession.id"
                        :options="{ width: 200 }"
                    ></vue-qrcode>

                    <div v-if="status === 'expired'" class="text-center p-4">
                        <p class="font-semibold text-red-600">
                            QR Code Kedaluwarsa
                        </p>
                        <PrimaryButton @click="generateQr" class="mt-4">
                            Buat Kode Baru
                        </PrimaryButton>
                    </div>

                    <div v-if="status === 'success'" class="text-center p-4">
                        <p class="font-semibold text-green-600">
                            Login Berhasil!
                        </p>
                        <p class="text-gray-500">Mengarahkan ke dashboard...</p>
                    </div>
                </div>

                <div class="mt-4 h-6">
                    <CountdownTimer
                        v-if="status === 'active' && qrSession"
                        :expires-at="qrSession.expires_at"
                        @finished="handleTimerFinished"
                    />
                </div>
            </div>
        </GuestLayout>
    </div>
</template>
