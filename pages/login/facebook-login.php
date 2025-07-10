<?php

$fb_app_id = '1809187140479341';
$redirect_uri = 'http://localhost/fbcallback';

$scope = 'public_profile,email';

$login_url = "https://www.facebook.com/v18.0/dialog/oauth?" . http_build_query([
    'client_id' => $fb_app_id,
    'redirect_uri' => $redirect_uri,
    'scope' => $scope,
    'response_type' => 'code',
]);

header("Location: $login_url");
exit;