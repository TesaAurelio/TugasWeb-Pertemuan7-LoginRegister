<?php
/**
 * edit-profil.php
 * BONUS: fitur tambahan di luar 8 requirement wajib.
 * Mengizinkan user mengubah nama dan (opsional) password.
 */

session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php';

$users = read_users();
$ditemukan = find_user_by_id($_SESSION['user_id'], $users);

if (!$ditemukan) {
    // Kasus langka: user terhapus dari JSON tapi session masih aktif
    header('Location: logout.php');
    exit;
}

$errors = [];
$namaLama = $ditemukan['data']['nama'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaBaru = sanitize_text($_POST['nama'] ?? '');
    $passwordBaru = $_POST['password_baru'] ?? '';
    $namaLama = $namaBaru; // supaya form tidak kosong lagi kalau error

    if ($namaBaru === '' || strlen($namaBaru) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    if ($passwordBaru !== '' && strlen($passwordBaru) < 6) {
        $errors[] = 'Password baru minimal 6 karakter (kosongkan kalau tidak ingin diubah).';
    }

    if (empty($errors)) {
        $users[$ditemukan['index']]['nama'] = $namaBaru;

        if ($passwordBaru !== '') {
            $users[$ditemukan['index']]['password_hash'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        save_users($users);

        // Perbarui juga data di session supaya dashboard langsung ikut berubah
        $_SESSION['user_name'] = $namaBaru;

        set_flash('success', 'Profil berhasil diperbarui.');
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil — Sistem Login/Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <main class="auth-page">
    <div class="auth-card">
      <h1 class="auth-card__title">Edit Profil</h1>
      <p class="auth-card__subtitle">Perbarui nama atau password akunmu.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
          <ul class="alert__list">
            <?php foreach ($errors as $pesan): ?>
              <li><?= htmlspecialchars($pesan) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="edit-profil.php" method="post" novalidate>
        <div class="auth-form__field">
          <label for="nama">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" required minlength="3"
                 value="<?= htmlspecialchars($namaLama) ?>">
        </div>

        <div class="auth-form__field">
          <label>Email</label>
          <input type="email" value="<?= htmlspecialchars($ditemukan['data']['email']) ?>" disabled>
          <small class="auth-form__hint">Email tidak dapat diubah.</small>
        </div>

        <div class="auth-form__field">
          <label for="password_baru">Password Baru (opsional)</label>
          <input type="password" id="password_baru" name="password_baru"
                 placeholder="Kosongkan jika tidak ingin mengubah password">
        </div>

        <button type="submit" class="button button--primary auth-form__submit">Simpan Perubahan</button>
      </form>

      <p class="auth-card__footer">
        <a href="dashboard.php">&larr; Kembali ke Dashboard</a>
      </p>
    </div>
  </main>

</body>
</html>
