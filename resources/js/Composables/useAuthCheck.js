let unlistenInertiaEvent = null;
import { onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";

export function setupAuthCheck() {
    onMounted(() => {
        unlistenInertiaEvent = router.on("navigate", checkAuthOnPageShow);
        window.addEventListener("pageshow", checkAuthOnPageShow);
    });
    onUnmounted(() => {
        if (unlistenInertiaEvent) {
            unlistenInertiaEvent();
        }
        window.removeEventListener("pageshow", checkAuthOnPageShow);
    });
}
const checkAuthOnPageShow = () => {
    console.log("Page show event triggered");
    router.reload({
        only: ["auth"],
        replace: true,
        preserveState: false,
        preserveScroll: true,
    });
};
