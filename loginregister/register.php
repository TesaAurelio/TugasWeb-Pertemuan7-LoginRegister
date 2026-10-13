<?php
/**
 * register.php
 * Requirement yang dipenuhi di file ini:
 *  1. Form registrasi dengan validasi (nama, email, password)
 *  3. Cek duplikasi email saat register
 *  2. Password di-hash dengan password_hash()
 *  6. Validasi input dan sanitasi
 */

session_start();
require_once __DIR__ . '/includes/functions.php';

// Kalau sudah login, tidak perlu register lagi
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['nama' => '', 'email' => '']; // menyimpan input lama supaya form tidak kosong lagi saat error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- SANITASI INPUT ----------
    $nama = sanitize_text($_POST['nama'] ?? '');
    $emailMentah = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $konfirmasiPassword = $_POST['konfirmasi_password'] ?? '';

    $old['nama'] = $nama;
    $old['email'] = $emailMentah;

    // ---------- VALIDASI ----------
    if ($nama === '' || strlen($nama) < 3) {
        $errors[] = 'Nama lengkap minimal 3 karakter.';
    }

    $email = sanitize_email($emailMentah);
    if ($email === null) {
        $errors[] = 'Format email tidak valid.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if ($password !== $konfirmasiPassword) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // ---------- CEK DUPLIKASI EMAIL ----------
    $users = read_users();
    if ($email !== null && find_user_by_email($email, $users) !== null) {
        $errors[] = 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.';
    }

    // ---------- SIMPAN JIKA VALID ----------
    if (empty($errors)) {
        $userBaru = [
            'id'             => generate_id(),
            'nama'           => $nama,
            'email'          => $email,
            'password_hash'  => password_hash($password, PASSWORD_DEFAULT),
            'remember_token' => null,
            'dibuat_pada'    => date('Y-m-d H:i:s'),
        ];

        $users[] = $userBaru;
        save_users($users);

        set_flash('success', 'Registrasi berhasil! Silakan login dengan akun barumu.');
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun — Sistem Login/Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <main class="auth-page">
    <div class="auth-card">
      <h1 class="auth-card__title">Buat Akun Baru</h1>
      <p class="auth-card__subtitle">Isi data di bawah untuk mendaftar.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
          <ul class="alert__list">
            <?php foreach ($errors as $pesan): ?>
              <li><?= htmlspecialchars($pesan) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="register.php" method="post" novalidate>
        <div class="auth-form__field">
          <label for="nama">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" required minlength="3"
                 value="<?= htmlspecialchars($old['nama']) ?>" placeholder="Nama lengkapmu">
        </div>

        <div class="auth-form__field">
          <label for="email">Alamat Email</label>
          <input type="email" id="email" name="email" required
                 value="<?= htmlspecialchars($old['email']) ?>" placeholder="nama@email.com">
        </div>

        <div class="auth-form__field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required minlength="6"
                 placeholder="Minimal 6 karakter">
        </div>

        <div class="auth-form__field">
          <label for="konfirmasi_password">Konfirmasi Password</label>
          <input type="password" id="konfirmasi_password" name="konfirmasi_password" required minlength="6"
                 placeholder="Ulangi password">
        </div>

        <button type="submit" class="button button--primary auth-form__submit">Daftar</button>
      </form>

      <p class="auth-card__footer">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
      </p>
    </div>
  </main>

</body>
</html>
