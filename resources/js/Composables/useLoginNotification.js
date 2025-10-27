import { usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted, watch } from "vue";

function hasWindow() {
  return typeof window !== "undefined";
}
function hasEcho() {
  return hasWindow() && window.Echo;
}
function hasNotif() {
  return hasWindow() && "Notification" in window;
}

async function ensureNotifyPermission() {
  if (!hasNotif()) return false;
  if (Notification.permission === "granted") return true;
  try {
    const res = await Notification.requestPermission();
    return res === "granted";
  } catch {
    return false;
  }
}

function osNotify({ title, body, url }) {
  if (!hasNotif() || Notification.permission !== "granted") return;
  const n = new Notification(title || "Notifikasi", { body: body || "" });
  n.onclick = () => {
    try {
      window.focus();
    } catch {}
    if (url) window.open(url, "_blank");
  };
}

// ==== STATE GLOBAL (singleton) ====
let activeUserId = null;
let leaveFn = null;
let started = false;
let refs = 0;

// subscribe ke channel user
async function subscribe(userId) {
  if (!hasEcho()) return;

  // lepas channel lama
  if (leaveFn) {
    try {
      leaveFn();
    } catch {}
    leaveFn = null;
  }
  activeUserId = null;

  if (!userId) return;

  const ch = window.Echo.private(`App.User.${userId}`);
  ch.listen("SessionLoggedIn", (e) => {
    osNotify({ title: e?.title, body: e?.body, url: e?.url });
  });

  leaveFn = () => window.Echo.leave(`App.User.${userId}`);
  activeUserId = userId;
}

/**
 * Panggil ini di layout atau halaman manapun
 * Bisa dipanggil berkali-kali (idempotent)
 */
export function setupLoginNotification() {
  const page = usePage();

  onMounted(async () => {
    refs += 1;

    if (!started) {
      started = true;
      await ensureNotifyPermission();
      const uid = page.props?.auth?.user?.id ?? null;
      await subscribe(uid);
    }

    const stopWatch = watch(
      () => page.props?.auth?.user?.id ?? null,
      async (newId, oldId) => {
        if (newId === oldId) return;
        await subscribe(newId);
      }
    );

    onUnmounted(() => {
      stopWatch();
      refs -= 1;
      if (refs <= 0) {
        if (leaveFn) {
          try {
            leaveFn();
          } catch {}
          leaveFn = null;
        }
        activeUserId = null;
        started = false;
        refs = 0;
      }
    });
  });
}
