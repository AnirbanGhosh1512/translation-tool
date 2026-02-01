<?php
$authUrl = "http://localhost:8081/realms/Translation/protocol/openid-connect/auth";

$params = http_build_query([
    "client_id" => "translation-spa",
    "response_type" => "code",
    "scope" => "openid profile",
    "redirect_uri" => "http://localhost:8000/callback.php"
]);

header("Location: $authUrl?$params");
exit;
