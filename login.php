<?php

require_once 'inc/config.inc.php';

if (isset($_GET['code'])) {

    $url_params = array(
        'code' => urldecode($_GET['code']),
        'grant_type' => 'authorization_code',
        'client_id' => $ig_client_id,
        'client_secret' => $ig_secret,
        'redirect_uri' => $ig_login_uri,
    );

    $ch = curl_init('https://api.instagram.com/oauth/access_token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($url_params));
    $auth = curl_exec($ch);

    echo '<pre>', print_r($auth, TRUE), '</pre>';

}
