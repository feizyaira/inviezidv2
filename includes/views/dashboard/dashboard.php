<?php require_once __DIR__ . '/../../../config/token/csrf_token.php'; ?>
<h1>Dashboard</h1>
<form action="/logout" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>"/>
    <button type="submit" name="logout">logout</button>
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if(isset($_SESSION['new_from_fb']) && $_SESSION['new_from_fb'] === true): ?>
<script>
    Swal.fire({
        title: 'Welcome!',
        text: 'Kamu mendaftar melalui Akun Facebook, silahkan verifikasi akun kamu!',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/account';
        }
    });
</script>
<?php endif; ?>