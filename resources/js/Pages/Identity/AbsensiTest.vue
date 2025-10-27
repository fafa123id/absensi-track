<script setup>
import { ref, onUnmounted } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { QrcodeStream } from "vue-qrcode-reader";
import axios from "axios";

// State untuk mengontrol UI
const scanResult = ref("");
const errorMessage = ref("");
const isScanning = ref(true); // Mulai dalam mode memindai

// Variabel untuk menyimpan stream kamera
let camera = null;

// Fungsi yang dipanggil saat QR code terdeteksi
const onDetect = async (detectedCodes) => {
    if (!isScanning.value) return;
    isScanning.value = false;
    scanResult.value = "Memverifikasi...";

    const sessionId = detectedCodes[0].rawValue;

    let publicId;
    try {
        const res = await fetch(sessionId, {
            headers: { Accept: "application/json" },
        });
        if (!res.ok) throw new Error("Gagal mengambil public Id QR.");
        const data = await res.json();
        publicId = typeof data === "string" ? data : data.identity;
    } catch (e) {
        console.error("Error saat fetch:", e);
        errorMessage.value = "QR tidak dapat dibaca.";
        isScanning.value = false;
        return;
    }

    try {
        const response = await axios.post(route("identity.qr.absen"), {
            id: publicId,
        });
        scanResult.value = response.data.message || "Absen berhasil disetujui!";
        errorMessage.value = "";
    } catch (error) {
        console.error("Error saat verifikasi:", error);
        scanResult.value = "";
        errorMessage.value =
            error.response?.data?.error ||
            "Kode QR tidak valid atau sudah kedaluwarsa.";
    }
};

// Fungsi untuk menangani error kamera
const onCameraError = (error) => {
    if (error.name === "NotAllowedError") {
        errorMessage.value =
            "Izin kamera ditolak. Harap izinkan akses kamera di pengaturan browser Anda.";
    } else {
        errorMessage.value = "Kamera tidak dapat diakses atau tidak ditemukan.";
    }
    isScanning.value = false;
};

// Fungsi untuk mencoba memindai lagi setelah error
const tryAgain = () => {
    errorMessage.value = "";
    scanResult.value = "";
    isScanning.value = true;
};

// Fungsi untuk menghentikan stream kamera
const turnOffCamera = () => {
    if (camera) {
        camera.stop();
    }
};

// Pastikan kamera dimatikan saat meninggalkan halaman
onUnmounted(() => {
    turnOffCamera();
});
</script>

<template>
    <Head title="QR Code Absen" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pindai untuk Absen
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex flex-col items-center">
                        <div
                            v-if="isScanning"
                            class="w-full max-w-sm border-4 border-gray-300 rounded-lg overflow-hidden"
                        >
                            <qrcode-stream
                                @detect="onDetect"
                                @error="onCameraError"
                                @camera-on="(cam) => (camera = cam)"
                            ></qrcode-stream>
                        </div>
                        <p v-if="isScanning" class="mt-4 text-gray-600">
                            Arahkan kamera ke QR code di layar perangkat lain.
                        </p>

                        <div v-if="scanResult" class="text-center gap-4">
                            <h3 class="text-2xl font-bold text-green-600">
                                Berhasil Absen!
                            </h3>
                            <p class="mt-2 text-gray-700">{{ scanResult }}</p>
                            <a
                                :href="route('dashboard')"
                                class="mt-4 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700"
                                >Kembali ke Dashboard</a
                            >
                        </div>

                        <div v-if="errorMessage" class="text-center">
                            <h3 class="text-2xl font-bold text-red-600">
                                Gagal!
                            </h3>
                            <p class="mt-2 text-gray-700">{{ errorMessage }}</p>
                            <button
                                @click="tryAgain"
                                class="mt-4 bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700"
                            >
                                Coba Pindai Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
