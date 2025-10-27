import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import mkcert from "vite-plugin-mkcert";
export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
            // fullUrl: true,
        }),
        vue({
            template: {
                transformAssetUrls: { base: null, includeAbsolute: false },
            },
        }),
        // mkcert TIDAK diaktifkan dulu supaya semua tetap HTTP
        // mkcert(), // aktifkan kalau semua sudah HTTPS
    ],
    // server: {
    //     host: true, // atau "0.0.0.0"
    //     port: 5173,
    //     strictPort: true,
    //     hmr: {
    //         host: "10.126.254.68",
    //         port: 5173,
    //         protocol: "ws", // pakai "wss" hanya kalau HTTPS
    //     },
    // },
    // server: {
    //     // Cukup ini. Biarkan Vite bisa diakses dari jaringan lokal.
    //     host: '0.0.0.0',
    // },
    // server: {
    //     host: "0.0.0.0",
    //     https: true, // WAJIB: Paksa Vite berjalan di HTTPS
    //     port: 5173,
    // },
});
