<?php
require_once __DIR__ . '/../../../config/token/csrf_token.php';

$errors = [];
$success = false;

if (isset($_POST['verify']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../../config/token/csrf_validate.php';

    $sellerEmail = trim($_POST['seller_email']);
    $otp = $_POST['otp'];

    $stmt = $pdo->prepare("SELECT * FROM sellers WHERE seller_email = ?");
    $stmt->execute([$sellerEmail]);
    $seller_data = $stmt->fetch();

    if (empty($sellerEmail)) {
        $errors[] = "Akun tidak diketahui";
    } elseif (!filter_var($sellerEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Terdapat kesalahan. Silahkan klik link dari email yang telah dikirmkan!";
    }

    if (empty($otp)) {
        $errors[] = "Masukkan kode OTP";
    }

    if ((int)$seller_data['otp'] !== (int)$otp) {
        $errors[] = "Kode OTP salah atau sesi telah berakhir";
    }

    if (empty($errors) && $seller_data['otp'] && $seller_data['otp_expires_at']) {
        $now = time();
        $expiresAt = strtotime($seller_data['otp_expires_at']);

        if ($expiresAt >= $now) {
            $update = $pdo->prepare("UPDATE sellers SET is_verified = '1', otp = NULL, otp_expires_at = NULL WHERE seller_email = ?");
            $update->execute([$sellerEmail]);

            $success = true;

            header('Location: /login');
            exit;
        } else {
            $errors[] = "Kode OTP sudah kadaluarsa. Siilahkan kirim ulang kode!";
        }
    }
}
?>
<div class="login">
    <div class="content">
        <section class="section greeting">
            <div class="feira">
                <div class="logo">
                    <img src="/assets/images/logo.png" alt="FA" class="image"/>
                </div>
                <div class="text">
                    <p class="f">feira</p>
                    <p class="s">studio</p>
                </div>
            </div>
            <div class="welcomes">
                <p class="greet">Hai, Selamat datang di</p>
                <h1 class="title">inviez.id</h1>
                <p class="desc">Admin Pannel</p>
            </div>
            <div class="illustration">
                <img src="/assets/images/illustration.png" alt="illustration" class="image"/>
            </div>
        </section>
        <section class="section log-form">
            <div class="form-title">
                <h2 class="title">Verifikasi</h2>
                <p class="desc">Silahkan masukkan kode OTP yang telah dikirimkan ke<br/><b><?= htmlspecialchars($_GET['email'] ?? '') ?></b></p>
            </div>
            <form class="form" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?? '' ?>"/>
                <input type="hidden" name="seller_email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>"/>
                <?php if (!empty($errors)): ?>
                <div class="input-group errors">
                    <ul class="list-column">
                        <?php foreach($errors as $error): ?>
                        <li class="error"><?= htmlspecialchars($error ?? '') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>"/>
                <div class="input-group">
                    <label class="label plain" for="inputOTP">
                        <span class="icon">123</span>
                        <input type="number" id="inputOTP" class="input" name="otp" placeholder="Kode OTP" required/>
                    </label>
                    <div class="error-text" id="error-inputOTP"></div>
                </div>
                <div class="input-group btn">
                    <button type="submit" class="button-default-plain-btn-primary" name="verify">
                        <span class="icon">send</span>
                        <span class="text">Verifikasi</span>
                    </button>
                </div>
                <div class="input-group btn">
                    <p class="navigated">Sudah punya akun? <a href="/login" class="link" title="Masuk">Masuk disini!</a></p>
                </div>
            </form>
        </section>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($success): ?>
<script>
    Swal.fire({
        title: 'Berhasil!',
        text: 'Akun kamu telah terverifikasi. Silahkan masuk!',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/login';
        }
    });
</script>
<?php endif; ?>
<?php if ($resend_success): ?>
<script>
    Swal.fire({
        title: 'Berhasil!',
        text: 'OTP sudah dikirim kembali. Cek email kamu!',
        icon: 'success',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>
<script>
    const inputOTP = document.getElementById('inputOTP');

    inputOTP.addEventListener('input', function () {
        const error = document.getElementById('error-inputOTP');
        const OTPPattern = /^[0-9]{6}$/;

        if(!OTPPattern.test(this.value)) {
            error.textContent = "Kode OTP tidak valid!";
        } else {
            error.textContent = "";
        }
    });
</script>