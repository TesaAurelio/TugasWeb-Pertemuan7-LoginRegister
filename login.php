<?php

session_start();
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if (empty($_SESSION['user_id']) && !empty($_COOKIE['remember_token'])) {
    $bagian = explode(':', $_COOKIE['remember_token'], 2);

    if (count($bagian) === 2) {
        [$userId, $token] = $bagian;
        $users = read_users();
        $ditemukan = find_user_by_id($userId, $users);

        if ($ditemukan && !empty($ditemukan['data']['remember_token'])
            && hash_equals($ditemukan['data']['remember_token'], $token)) {
            $_SESSION['user_id']    = $ditemukan['data']['id'];
            $_SESSION['user_name']  = $ditemukan['data']['nama'];
            $_SESSION['user_email'] = $ditemukan['data']['email'];

            header('Location: dashboard.php');
            exit;
        }
    }
    setcookie('remember_token', '', time() - 3600, '/');
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $emailMentah = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $ingatSaya = isset($_POST['ingat_saya']);
    $oldEmail = $emailMentah;

    $email = sanitize_email($emailMentah);

    if ($email === null) {
        $errors[] = 'Format email tidak valid.';
    }

    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    }

    if (empty($errors)) {
        $users = read_users();
        $ditemukan = $email !== null ? find_user_by_email($email, $users) : null;

        if (!$ditemukan || !password_verify($password, $ditemukan['data']['password_hash'])) {
            $errors[] = 'Email atau password salah.';
        } else {
            $_SESSION['user_id']    = $ditemukan['data']['id'];
            $_SESSION['user_name']  = $ditemukan['data']['nama'];
            $_SESSION['user_email'] = $ditemukan['data']['email'];

            if ($ingatSaya) {
                $token = generate_token();
                $users[$ditemukan['index']]['remember_token'] = $token;
                save_users($users);

                $nilaiCookie = $ditemukan['data']['id'] . ':' . $token;
                setcookie('remember_token', $nilaiCookie, time() + (30 * 24 * 60 * 60), '/');
            }

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk — Sistem Login/Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <main class="auth-page">
    <div class="auth-card">
      <h1 class="auth-card__title">Masuk ke Akunmu</h1>
      <p class="auth-card__subtitle">Silakan login untuk melanjutkan.</p>

      <?php include __DIR__ . '/includes/flash_display.php'; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
          <ul class="alert__list">
            <?php foreach ($errors as $pesan): ?>
              <li><?= htmlspecialchars($pesan) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="login.php" method="post" novalidate>
        <div class="auth-form__field">
          <label for="email">Alamat Email</label>
          <input type="email" id="email" name="email" required
                 value="<?= htmlspecialchars($oldEmail) ?>" placeholder="nama@email.com">
        </div>

        <div class="auth-form__field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required placeholder="Password">
        </div>

        <div class="auth-form__checkbox">
          <input type="checkbox" id="ingat_saya" name="ingat_saya">
          <label for="ingat_saya">Ingat saya selama 30 hari</label>
        </div>

        <button type="submit" class="button button--primary auth-form__submit">Masuk</button>
      </form>

      <p class="auth-card__footer">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
      </p>
    </div>
  </main>

</body>
</html>
