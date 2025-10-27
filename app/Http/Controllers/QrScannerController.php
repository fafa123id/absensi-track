<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class QrScannerController extends Controller
{
    /**
     * Menampilkan halaman pemindai QR code.
     * Method ini hanya akan dipanggil jika user sudah login.
     */
    public function __invoke()
    {
        return Inertia::render('Auth/QrScanner');
    }
}