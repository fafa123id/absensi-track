import { router, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { showError } from "./swal";
const page = usePage();
export const setupPrivateAuthWebsocket = () => {
    onMounted(() => {
        const session = page.props.currentSessionId;
        const user = page.props.auth.user;
        if (user) {
            window.Echo.private(`App.User.${user.id}`).listen(
                "SessionLoggedOut",
                (event) => {
                    if (event.sessionId === session) {
                        showError(
                            "Sesi anda telah berakhir!",
                            "Silahkan login kembali."
                        );
                        router.reload();
                    }
                }
            );
        }
    });
};
