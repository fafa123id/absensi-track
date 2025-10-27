<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    /**
     * Waktu kedaluwarsa dalam format string yang bisa dibaca JavaScript.
     * Contoh: '2025-10-26T10:00:00Z' atau output dari `now()->addMinutes(2)->toIso8601String()` di Laravel.
     */
    expiresAt: {
        type: String,
        required: true,
    }
});

const emit = defineEmits(['finished']);

const remainingSeconds = ref(0);
let timer = null;

// Fungsi utama untuk menghitung sisa waktu
const updateRemainingSeconds = () => {
    if (!props.expiresAt) {
        remainingSeconds.value = 0;
        return;
    }

    const expirationTime = new Date(props.expiresAt).getTime();
    const now = new Date().getTime();
    const difference = expirationTime - now;

    // Jika waktu sudah habis
    if (difference <= 0) {
        remainingSeconds.value = 0;
        clearInterval(timer); // Hentikan interval
        emit('finished'); // Kirim event bahwa timer selesai
        return;
    }

    // Update sisa detik
    remainingSeconds.value = Math.ceil(difference / 1000);
};

// Fungsi untuk memulai timer
const startTimer = () => {
    // Hentikan timer lama jika ada
    if (timer) {
        clearInterval(timer);
    }
    // Panggil sekali di awal agar tidak ada jeda 1 detik
    updateRemainingSeconds();
    // Set interval untuk mengupdate setiap detik
    timer = setInterval(updateRemainingSeconds, 1000);
};

// Awasi perubahan pada prop 'expiresAt', lalu mulai ulang timer
watch(() => props.expiresAt, () => {
    startTimer();
});

// Mulai timer saat komponen pertama kali dimuat
onMounted(() => {
    startTimer();
});

// Pastikan untuk membersihkan interval saat komponen dihancurkan untuk mencegah memory leak
onUnmounted(() => {
    clearInterval(timer);
});
</script>

<template>
    <div class="inline-block text-sm font-medium">
        <span v-if="remainingSeconds > 0" class="text-gray-700">
            (Kedaluwarsa dalam {{ remainingSeconds }} detik)
        </span>
        <span v-else class="text-red-600">
            (Waktu habis)
        </span>
    </div>
</template>