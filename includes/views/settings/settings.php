<?php require_once __DIR__ . '/../../../config/token/csrf_token.php' ?>

<form action="/config/token/unlink-google.php" method="POST" onsubmit="return confirm('Kamu yakin mau unlink akun Google dari sistem?');">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <button type="submit" class="btn btn-danger" name="unlinkGoogle">Unlink Google</button>
</form>
