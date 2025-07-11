<?php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOTP($to, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'feizyaira@gmail.com';
        $mail->Password = 'pgtpnwpudkzyftoo';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('feizyaira@gmail.com', 'InviezID Admin');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Kode OTP Verifikasi InviezID Admin';
        $mail->Body = "Terimakasih telah mendaftar. Berikut adalah kode OTP verifikasi akun kamu:<br/><b>$otp</b><br/><br/>Kode OTP berlaku selama <strong>10 menit.</strong>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function forgotPassword($to, $resetToken) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'feizyaira@gmail.com';
        $mail->Password = 'pgtpnwpudkzyftoo';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($env['MAILER_USERNAME'] ?? getenv('MAILER_USERNAME'), 'InviezID Admin');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Link Pemulihan Kata Sandi';
        $mail->Body = "
        <div style='display:block;padding:25px'>
            <header style='display:block;background-color:#a594f9'>
                <p style='color:#f5f5f5'>Berikut adalah link untuk perubahan kata sandi kamu:</p>
            </header><br/>
            <main style='display:block;width:100%'>
                <a href='https://inviez.my-board.org/reset?token_code=$resetToken'>https://inviez.my-board.org/reset?token_code=$resetToken</a>
            </main>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}