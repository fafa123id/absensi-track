<script setup>
import { Head } from "@inertiajs/vue3";

// Terima prop 'company' dari controller
const props = defineProps({
    company: Object,
});
</script>

<template>
    <Head :title="'Identitas Perusahaan: ' + (company ? company.name : 'Perusahaan')" />

    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 p-4"
    >
        <div class="w-full max-w-2xl text-center space-y-8">
            <div class="animate-fade-in-down">
                <h1 class="text-3xl sm:text-4xl font-light text-gray-700 mb-1">
                    Selamat Datang di Portal Identitas Perusahaan
                </h1>
                <p
                    class="text-5xl sm:text-6xl md:text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 py-2"
                >
                    {{ company?.name || "Perusahaan Anda" }}
                </p>
            </div>

            <div
                v-if="company"
                class="bg-white/70 backdrop-blur-md rounded-xl shadow-lg p-6 sm:p-8 space-y-4 animate-fade-in delay-500"
            >
                <h2
                    class="text-xl font-semibold text-gray-800 border-b pb-2 mb-4"
                >
                    Informasi Perusahaan
                </h2>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left text-gray-700"
                >
                    <div class="animate-fade-in-left delay-700">
                        <strong class="block text-gray-500">Email:</strong>
                        <span>{{ company.email || "-" }}</span>
                    </div>
                    <div class="animate-fade-in-right delay-700">
                        <strong class="block text-gray-500">Alamat:</strong>
                        <p>{{ company.address || "-" }}</p>
                    </div>
                </div>
            </div>
            <p v-else class="text-gray-500 animate-fade-in delay-500">
                Memuat informasi perusahaan...
            </p>
        </div>
    </div>
</template>

<style>
/* Animasi Dasar (bisa ditaruh di app.css global) */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.8s ease-out forwards;
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}
.animate-fade-in-left {
    animation: fadeInLeft 0.8s ease-out forwards;
}
.animate-fade-in-right {
    animation: fadeInRight 0.8s ease-out forwards;
}
.animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
}

/* Utility Classes for Delay (bisa ditaruh di app.css atau tailwind.config.js) */
.delay-500 {
    animation-delay: 0.5s;
    opacity: 0;
} /* Opacity 0 agar tidak kelihatan sebelum delay */
.delay-700 {
    animation-delay: 0.7s;
    opacity: 0;
}
.delay-900 {
    animation-delay: 0.9s;
    opacity: 0;
}
</style>
