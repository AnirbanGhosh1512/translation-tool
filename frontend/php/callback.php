<?php
if (!isset($_GET['code'])) {
    die("No authorization code");
}

$tokenUrl = "http://localhost:8081/realms/Translation/protocol/openid-connect/token";

$data = [
    "grant_type" => "authorization_code",
    "client_id" => "translation-spa",
    "code" => $_GET['code'],
    "redirect_uri" => "http://localhost:8000/callback.php"
];

$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded",
        "method" => "POST",
        "content" => http_build_query($data)
    ]
];

$response = file_get_contents($tokenUrl, false, stream_context_create($options));
$token = json_decode($response, true);

session_start();
$_SESSION["access_token"] = $token["access_token"];

header("Location: index.php");
exit;
