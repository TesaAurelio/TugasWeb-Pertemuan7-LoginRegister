<?php
/**
 * logout.php
 * Requirement yang dipenuhi di file ini:
 *  7. Logout menghapus session (session_destroy)
 */

session_start();
require_once __DIR__ . '/includes/functions.php';

// Kalau user sebelumnya pakai "Remember Me", hapus juga token
// yang tersimpan di data/users.json supaya cookie lama tidak
// bisa dipakai login otomatis lagi (keamanan).
if (!empty($_SESSION['user_id'])) {
    $users = read_users();
    $ditemukan = find_user_by_id($_SESSION['user_id'], $users);
    if ($ditemukan) {
        $users[$ditemukan['index']]['remember_token'] = null;
        save_users($users);
    }
}

// Hapus cookie remember-me di browser
if (!empty($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Hapus seluruh data session dan session itu sendiri
$_SESSION = [];
session_unset();
session_destroy();

// Mulai session baru HANYA untuk menampilkan flash message di halaman login
session_start();
set_flash('success', 'Kamu berhasil logout. Sampai jumpa lagi!');

header('Location: login.php');
exit;
