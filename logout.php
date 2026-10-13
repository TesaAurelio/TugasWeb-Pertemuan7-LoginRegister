<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    $users = read_users();
    $ditemukan = find_user_by_id($_SESSION['user_id'], $users);
    if ($ditemukan) {
        $users[$ditemukan['index']]['remember_token'] = null;
        save_users($users);
    }
}

if (!empty($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

$_SESSION = [];
session_unset();
session_destroy();

session_start();
set_flash('success', 'Kamu berhasil logout. Sampai jumpa lagi!');

header('Location: login.php');
exit;
