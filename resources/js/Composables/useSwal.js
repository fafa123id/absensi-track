import { onMounted, onUnmounted, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { showSuccess, showError } from "@/Composables/swal";

const page = usePage();
// const canShowFlash = ref(true);
export const setupSwalFlashMessages = () => {
    watch(
        () => page.props.flash.success,
        (newMessage) => {
            if (newMessage) {
                showSuccess("Berhasil!", newMessage);
                page.props.flash.success = null;
            }
        },
        { immediate: true }
    );
    watch(
        () => page.props.flash.error,
        (newMessage) => {
            if (newMessage) {
                showError("Gagal!", newMessage);
                page.props.flash.error = null;
            }
        },
        { immediate: true }
    );
};
