<?php
require_once "auth.php";

function apiRequest($method, $endpoint, $body = null) {
    $token = getAccessToken();
    $url = "http://localhost:5294" . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);

    if ($response === false) {
        die("API error: " . curl_error($ch));
    }

    curl_close($ch);

    return json_decode($response, true);
}
