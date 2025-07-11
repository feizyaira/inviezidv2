<?php require_once __DIR__ . '/../../../config/token/csrf_token.php'; ?>
<h1>Dashboard</h1>
<form action="/logout" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"/>
    <button type="submit" name="logout">logout</button>
</form>