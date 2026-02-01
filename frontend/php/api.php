<?php
session_start();

$token = $_SESSION["access_token"] ?? null;
if (!$token) {
    die("Not logged in");
}

$ch = curl_init("http://localhost:5294/api/translations");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token"
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

echo "<pre>";
echo htmlspecialchars($response);
echo "</pre>";
