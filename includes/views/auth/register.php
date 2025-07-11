<?php
error_reporting(E_ALL);
require_once __DIR__ . '/../../../config/token/csrf_token.php';
require __DIR__ . '/../../../config/helper/mailer.php';

$errors = [];
$success_send = false;
$failed_send = false;

if (isset($_POST['register']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../../config/token/csrf_validate.php';

    $sellerName = trim($_POST['seller_name']);
    $sellerEmail = trim($_POST['seller_email']);
    $sellerPwd = $_POST['seller_pwd'];
    $sellerConfirm = $_POST['seller_confirm'];
    $accepted = $_POST['accept'];
    $otp = random_int(100000, 999999);

    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    if (empty($sellerName)) {
        $errors[] = "Kolom nama wajib diisi!";
    } elseif (!preg_match("/^[a-zA-Z\s]{4,30}$/", $sellerName)) {
        $errors[] = "Nama hanya diizinkan menggunakan karakter alfabet. Minimal 4-30 karakter!";
    }

    if (empty($sellerEmail)) {
        $errors[] = "Alamat email wajib diisi!";
    } elseif (!filter_var($sellerEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Alamat email tidak valid!";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sellers WHERE seller_email = ?");
        $stmt->execute([$sellerEmail]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Alamat email sudah terdaftar";
        }
    }

    if (empty($sellerPwd)) {
        $errors[] = "Harap masukkan kata sandi!";
    } elseif (strlen($sellerPwd) < 6) {
        $errors[] = "Kata sandi minimal 6 karakter";
    }

    if (empty($sellerConfirm)) {
        $errors[] = "Konfirmasikan kata sandi!";
    }
    
    if ($sellerConfirm != $sellerPwd) {
        $errors[] = "Kata sandi tidak cocok";
    }

    if (empty($accepted)) {
        $errors[] = "Kamu wajib menyetujui Kebijakan Layanan dan Privasi untuk melanjutkan pendaftaran!";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($sellerPwd, PASSWORD_BCRYPT);

        $insertSeller = $pdo->prepare("INSERT INTO sellers (seller_name, seller_email, seller_pwd, otp, otp_expires_at) VALUES (?, ?, ?, ?, ?)");
        $insertSeller->execute([$sellerName, $sellerEmail, $hashed_password, $otp, $expiresAt]);

        if (sendOTP($sellerEmail, $otp)) {
            echo "Success";
        } else {
            echo "Failed";
        }

        $urlemail = urlencode($sellerEmail);
        header("Location: /verify?email=$urlemail");
        exit;
    } else {
        $errors[] = "Why";
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
                <h2 class="title">Daftar</h2>
                <p class="desc">Silahkan isi kolom dibawah ini untuk mendaftar!</p>
            </div>
            <form id="regForm" class="form" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?? '' ?>"/>
                <?php if (!empty($errors)): ?>
                <div class="input-group errors">
                    <ul class="list-column">
                        <?php foreach($errors as $error): ?>
                        <li class="error"><?= htmlspecialchars($error ?? '') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <div class="input-group">
                    <label class="label plain" for="inputName">
                        <span class="icon">person</span>
                        <input type="text" id="inputName" class="input" name="seller_name" placeholder="Nama Lengkap" required/>
                    </label>
                    <div class="error-text" id="error-inputName"></div>
                </div>
                <div class="input-group">
                    <label class="label plain" for="inputEmail">
                        <span class="icon">mail</span>
                        <input type="email" id="inputEmail" class="input" name="seller_email" placeholder="Alamat Email" required/>
                    </label>
                    <div class="error-text" id="error-inputEmail"></div>
                </div>
                <div class="input-group">
                    <label class="label plain" for="inputPassword">
                        <span class="icon">password</span>
                        <input type="password" id="inputPassword" class="input" name="seller_pwd" placeholder="Kata Sandi" required/>
                    </label>
                    <div class="error-text" id="error-inputPassword"></div>
                </div>
                <div class="input-group">
                    <label class="label plain" for="inputConfirm">
                        <span class="icon">password</span>
                        <input type="password" id="inputConfirm" class="input" name="seller_confirm" placeholder="Konfirmasi Kata Sandi" required/>
                    </label>
                    <div class="error-text" id="error-inputConfirm"></div>
                </div>
                <div class="input-group check">
                    <input id="inputAccept" type="checkbox" name="accept" required/>
                    <label class="label-check" for="Accept"><small><i>Saya telah membaca dan menyetujui <a href="/tos" title="Kebijakan Layanan">Kebijakan Layanan</a> dan <a href="/privacy" title="Kebijakan Privasi">Kebijakan Privasi</a> dari <a href="/" title="InviezID">InviezID</a>.</i></small></label>
                    <div class="error-text" id="error-inputAccept"></div>
                </div>
                <div class="input-group btn">
                    <button type="submit" class="button-default-plain-btn-primary" name="register">
                        <span class="icon">how_to_reg</span>
                        <span class="text">Daftar</span>
                    </button>
                </div>
                <span class="or">- ATAU -</span>
                <div class="input-group btn">
                    <a href="/fb-login" class="social-login fb">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" class="main-grid-item-icon" fill="none">
                          <path d="m17.543 13.398.661-4.31h-4.136V6.29c0-1.18.578-2.329 2.43-2.329h1.88V.291S16.673 0 15.042 0c-3.407 0-5.633 2.064-5.633 5.802v3.285H5.622v4.311h3.786v10.42a15.015 15.015 0 0 0 4.66 0v-10.42h3.475Z" fill="#1877F2" />
                        </svg>
                        <span class="text">Daftar dengan Facebook</span>
                    </a>
                    <a href="/google-login" class="social-login google">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" class="main-grid-item-icon" fill="none">
                          <path d="M24 12.276c0-.816-.067-1.636-.211-2.438H12.242v4.62h6.612a5.549 5.549 0 0 1-2.447 3.647v2.998h3.945C22.669 19.013 24 15.927 24 12.276Z" fill="#4285F4" />
                          <path d="M12.241 24c3.302 0 6.086-1.063 8.115-2.897l-3.945-2.998c-1.097.732-2.514 1.146-4.165 1.146-3.194 0-5.902-2.112-6.873-4.951H1.302v3.09C3.38 21.444 7.612 24 12.242 24Z" fill="#34A853" />
                          <path d="M5.369 14.3a7.053 7.053 0 0 1 0-4.595v-3.09H1.302a11.798 11.798 0 0 0 0 10.776L5.369 14.3Z" fill="#FBBC04" />
                          <path d="M12.241 4.75a6.727 6.727 0 0 1 4.696 1.798l3.495-3.425A11.898 11.898 0 0 0 12.243 0C7.611 0 3.38 2.558 1.301 6.615l4.067 3.09C6.336 6.862 9.048 4.75 12.24 4.75Z" fill="#EA4335" />
                        </svg>
                        <span class="text">Daftar dengan Google</span>
                    </a>
                </div>
                <div class="input-group btn">
                    <p class="navigated">Sudah punya akun? <a href="/login" class="link" title="Masuk">Masuk disini!</a></p>
                </div>
            </form>
        </section>
    </div>
</div>
<script>
    const inputName = document.getElementById('inputName');
    const inputEmail = document.getElementById('inputEmail');
    const inputPassword = document.getElementById('inputPassword');
    const inputConfirm = document.getElementById('inputConfirm');
    const inputAccept = document.getElementById('inputAccept');

    inputName.addEventListener('input', function () {
      const error = document.getElementById('error-inputName');
      const value = this.value;

      const onlyLetters = /^[A-Za-z\s]+$/.test(value);
      const noDoubleSpaces = !/\s{2,}/.test(value);
      const noSpaceInOut = value.trim() === value;
      const textLength = value.length >= 4 && value.length <= 50;

      if (!onlyLetters) {
        error.textContent = "Gunakan huruf alfabet";
      } else if (!noDoubleSpaces) {
        error.textContent = "Format nama salah";
      } else if (!noSpaceInOut) {
        error.textContent = "Format nama salah";
      } else if (!textLength) {
        error.textContent = "Minimal 4 sampai 50 huruf";
      } else {
        error.textContent = "";
      }
    });

    inputEmail.addEventListener('input', function () {
        const error = document.getElementById('error-inputEmail');
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

        if(!emailPattern.test(this.value)) {
            error.textContent = "Alamat emaiil tidak valid!";
        } else {
            error.textContent = "";
        }
    });

    inputPassword.addEventListener('input', function () {
        const error = document.getElementById('error-inputPassword');

        if (this.value.length < 6) {
            error.textContent = "Kata Sandi minimal 6 karakter!";
        } else {
            error.textContent = "";
        }
    });

    inputConfirm.addEventListener('input', function() {
      const error = document.getElementById('error-inputConfirm');
      if (this.value != inputPassword.value) {
        error.textContent = "Kata sandi tidak cocok";
      } else {
        error.textContent = "";
      }
    });

    inputAccept.addEventListener('input', function() {
        const error = document.getElementById('error-inputAccept');
        if (!this.checked) {
            error.textContent = "Kamu wajib menyetujui kebijakan dari InviezID";
        } else {
            error.textContent = "";
        }
    });

    document.getElementById('regForm').addEventListener('submit', function (e) {
      const isNamaValid = inputName.value.length >= 4 && inputName.value.length <= 50;
      const isEmailValid = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/.test(inputEmail.value);
      const isPasswordValid = inputPassword.value.length >= 6;
      const iscPasswordValid = inputPassword.value == inputConfirm.value;
      const isCheckboxChecked = inputAccept.checked;

      if (!isNamaValid || !isEmailValid || !isPasswordValid || !iscPasswordValid || !isCheckboxChecked) {
        e.preventDefault();
        if (!isCheckboxChecked) {
          document.getElementById('error-inputAccept').textContent = "Kamu wajib menyetujui Syarat & Ketentuan";
        }
      }
    });
</script>