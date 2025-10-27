<script setup>
import { ref, onUnmounted, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { QrcodeStream } from "vue-qrcode-reader";
import axios from "axios";

// --- UI state ---
const scanResult = ref("");
const errorMessage = ref("");
const isScanning = ref(true);

// --- Camera state ---
const camera = ref(null);
const cameras = ref([]);
const currentCameraIndex = ref(0);
const cameraConstraints = ref({ facingMode: "environment" }); // default: kamera belakang
const cameraError = ref("");

async function loadCameras() {
    try {
        await navigator.mediaDevices.getUserMedia({ video: true }).then(s => s.getTracks().forEach(t => t.stop()));
        const devices = await navigator.mediaDevices.enumerateDevices();
        cameras.value = devices.filter((d) => d.kind === "videoinput");
        if (
            cameras.value.length &&
            currentCameraIndex.value >= cameras.value.length
        ) {
            currentCameraIndex.value = 0;
        }
    } catch (err) {
        console.error("Tidak bisa mendeteksi kamera:", err);
        errorMessage.value = "Tidak dapat mendeteksi perangkat kamera.";
        isScanning.value = false;
    }
}

function switchCamera() {
    if (cameras.value.length <= 1) return;
    currentCameraIndex.value =
        (currentCameraIndex.value + 1) % cameras.value.length;
    const deviceId = cameras.value[currentCameraIndex.value].deviceId;
    cameraConstraints.value = { deviceId: { exact: deviceId } };
    // reset error & pastikan scanning aktif
    errorMessage.value = "";
    isScanning.value = true;
}

// QR detect
const onDetect = async (detectedCodes) => {
    if (!isScanning.value) return;
    isScanning.value = false;
    scanResult.value = "Memverifikasi...";

    const sessionId = detectedCodes[0].rawValue;

    try {
        const response = await axios.post(route("qr.scan"), { id: sessionId });
        scanResult.value = response.data.message || "Login berhasil disetujui!";
        errorMessage.value = "";
    } catch (error) {
        scanResult.value = "";
        errorMessage.value =
            error.response?.data?.error ||
            "Kode QR tidak valid atau sudah kedaluwarsa.";
    }
};

// Kamera error
const onCameraError = (error) => {
    console.warn("Kamera error:", error);
    if (error?.name === "NotAllowedError") {
        errorMessage.value =
            "Izin kamera ditolak. Harap izinkan akses kamera di pengaturan browser Anda.";
    } else {
        errorMessage.value = "Kamera tidak dapat diakses atau tidak ditemukan.";
        cameraError.value = true;
    }
    isScanning.value = false;
};

// Coba lagi → balikin ke state awal yang valid
const tryAgain = () => {
    errorMessage.value = "";
    scanResult.value = "";
    isScanning.value = true;
    // kembalikan ke default: kamera belakang
    cameraConstraints.value = { facingMode: "environment" };
    // muat ulang daftar kamera (kalau user baru kasih izin)
    loadCameras();
};

// Matikan kamera (stop semua track biar aman)
const turnOffCamera = () => {
    const stream = camera.value;
    if (stream?.getTracks) {
        stream.getTracks().forEach((t) => t.stop());
    } else if (stream?.stop) {
        stream.stop();
    }
};

onMounted(loadCameras);
onUnmounted(turnOffCamera);
</script>

<template>
    <Head title="QR Code Scan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pindai untuk Login
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex flex-col items-center">
                        <!-- Scanner -->
                        <div
                            v-if="isScanning"
                            class="w-full max-w-sm border-4 border-gray-300 rounded-lg overflow-hidden relative"
                        >
                            <qrcode-stream
                                :constraints="cameraConstraints"
                                @detect="onDetect"
                                @error="onCameraError"
                                @camera-on="(cam) => (camera.value = cam)"
                            />

                            <!-- Tombol switch camera -->
                            <div
                                v-if="cameras.length > 1"
                                class="absolute top-2 right-2 z-10"
                            >
                                <button
                                    @click="switchCamera"
                                    class="bg-gray-800 text-white px-3 py-1 rounded-md text-sm hover:bg-gray-700 transition"
                                >
                                    Ganti Kamera
                                </button>
                            </div>
                        </div>

                        <p v-if="isScanning" class="mt-4 text-gray-600">
                            Arahkan kamera ke QR code di layar perangkat lain.
                        </p>

                        <!-- Berhasil -->
                        <div v-if="scanResult" class="text-center gap-4">
                            <h3 class="text-2xl font-bold text-green-600">
                                Berhasil!
                            </h3>
                            <p class="mt-2 text-gray-700">{{ scanResult }}</p>
                            <a
                                :href="route('qr.scanner')"
                                class="mt-4 inline-block bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 mr-4"
                                >Scan Ulang</a
                            >
                            <a
                                :href="route('dashboard')"
                                class="mt-4 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600"
                                >Kembali ke Dashboard</a
                            >
                        </div>

                        <!-- Error -->
                        <div v-if="errorMessage" class="text-center">
                            <h3 class="text-2xl font-bold text-red-600">
                                Gagal!
                            </h3>
                            <p class="mt-2 text-gray-700">{{ errorMessage }}</p>
                            <div
                                class="mt-4 flex items-center justify-center gap-3"
                            >
                                <button v-if="!cameraError"
                                    @click="tryAgain"
                                    class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600"
                                >
                                    Coba Pindai Lagi
                                </button>
                                <button
                                    v-if="cameras.length > 1"
                                    @click="switchCamera"
                                    class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700"
                                >
                                    Ganti Kamera
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
