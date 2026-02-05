<?php
require 'auth.php';
$token = getAccessToken();

$data = json_decode(file_get_contents("php://input"), true);

$url = "http://localhost:5294/api/translations/{$data['sid']}/{$data['langId']}";

$payload = json_encode([
    "sid" => $data['sid'],
    "langId" => $data['langId'],
    "text" => $data['text']
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => $payload
]);

curl_exec($ch);
curl_close($ch);
