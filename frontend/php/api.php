<?php
require 'auth.php';

function apiRequest($method, $url, $body = null) {
    $token = getAccessToken();

    $ch = curl_init($url);
    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ];

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers
    ]);

    if ($body) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$status, $response];
}
