<?php

// Turns error reporting up to eleven
error_reporting(E_ALL^E_STRICT);
ini_set('display_errors', 1);

session_start();

// Instagram API credentials
$ig_client_id = '';
$ig_secret    = '';
$ig_login_uri = 'http://example.com//login.php';

// Pusher API credentials
$pusher_key    = '';
$pusher_secret = '';
$pusher_app_id = '';
