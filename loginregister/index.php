<?php
/**
 * index.php
 * Pintu masuk situs. Mengarahkan otomatis sesuai status login.
 */

session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
