<?php
/**
 * includes/auth_check.php
 *
 * Dipanggil di awal halaman yang WAJIB login (contoh: dashboard.php).
 * Kalau session user belum ada, paksa redirect ke login.php.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    set_flash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
    header('Location: login.php');
    exit;
}
