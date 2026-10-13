<?php
/**
 * includes/flash_display.php
 * Menampilkan pesan sukses/error dari session (kalau ada), lalu otomatis
 * terhapus setelah ditampilkan (lihat get_flash() di functions.php).
 */
$flash = get_flash();
if ($flash): ?>
  <div class="alert alert--<?= htmlspecialchars($flash['type']) ?>">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
<?php endif; ?>
