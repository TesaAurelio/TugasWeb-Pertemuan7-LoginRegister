<?php

$flash = get_flash();
if ($flash): ?>
  <div class="alert alert--<?= htmlspecialchars($flash['type']) ?>">
    <?= htmlspecialchars($flash['message']) ?>
  </div>
<?php endif; ?>
