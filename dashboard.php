<?php

session_start();
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php'; 

$users = read_users();
$user = find_user_by_id($_SESSION['user_id'], $users);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Sistem Login/Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <main class="dashboard-page">
    <div class="dashboard-card">

      <?php include __DIR__ . '/includes/flash_display.php'; ?>

      <h1 class="dashboard-card__title">
        Halo, <?= htmlspecialchars($_SESSION['user_name']) ?>! 👋
      </h1>
      <p class="dashboard-card__subtitle">Selamat datang kembali di dashboard-mu.</p>

      <dl class="dashboard-card__info">
        <dt>Nama</dt>
        <dd><?= htmlspecialchars($_SESSION['user_name']) ?></dd>

        <dt>Email</dt>
        <dd><?= htmlspecialchars($_SESSION['user_email']) ?></dd>

        <?php if ($user): ?>
        <dt>Bergabung sejak</dt>
        <dd><?= htmlspecialchars($user['data']['dibuat_pada']) ?></dd>
        <?php endif; ?>
      </dl>

      <div class="dashboard-card__actions">
        <a href="edit-profil.php" class="button button--ghost">Edit Profil</a>
        <a href="logout.php" class="button button--primary">Logout</a>
      </div>

    </div>
  </main>

</body>
</html>
