export function hasShownFlash(key) {
    return sessionStorage.getItem(`flash_shown_${key}`) === '1';
}

export function markFlashAsShown(key) {
    sessionStorage.setItem(`flash_shown_${key}`, '1');
}

export function clearFlash(key) {
    sessionStorage.removeItem(`flash_shown_${key}`);
}
