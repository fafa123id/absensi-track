import Swal from 'sweetalert2';

export function confirmAction(title = 'Anda yakin?', text = 'Tindakan ini tidak dapat dibatalkan!', icon = 'warning') {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, lanjutkan!',
        cancelButtonText: 'Batal'
    });
}

export function showSuccess(title = 'Berhasil!', text = 'Data Anda telah disimpan.') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
}
export function showError(title = 'Gagal!', text = 'Terjadi kesalahan. Silakan coba lagi.') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'error',
        timer: 2000,
        showConfirmButton: false
    });
}