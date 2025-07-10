<?php
session_start();

$fb_app_id = '1809187140479341';
$fb_app_secret = '205fce7a19d255f39af3019d81dfd945';
$redirect_uri = 'http://localhost/fbcallback';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $token_url = "https://graph.facebook.com/v18.0/oauth/access_token?" . http_build_query([
        'client_id' => $fb_app_id,
        'redirect_uri' => $redirect_uri,
        'client_secret' => $fb_app_secret,
        'code' => $code
    ]);

    $response = file_get_contents($token_url);
    $data = json_decode($response, true);

    //echo "<pre>";
    //print_r($data);
    //echo "</pre>";
    //exit;

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];

        $graph_url = "https://graph.facebook.com/me?fields=id,name,email&access_token=$access_token";
        $user = json_decode(file_get_contents($graph_url), true);

        $fb_id = $user['id'] ?? '';
        $fb_name = $user['name'] ?? '';
        $fb_email = $user['email'] ?? '';

        if (!$fb_email) {
            $_SESSION['fb_id'] = $fb_id;
            $_SESSION['fb_name'] = $fb_name;

            header('Location: /signup-from-facebook');
            exit;
        }

        $sellerEmail = $fb_email;
        require_once __DIR__ . '/../database/registered-user.php';

        if ($checkUser < 1) {
            header('Location: /signup-from-facebook');
            exit;
        } else {
            header('Location: /dashboard');
            exit;
        }
        
    } else {
        echo "Gagal ambil token";
        print_r($data);
    }
} else {
    echo "Gak ada code dari facebook";
}