<?php

function getAccessToken()
{
    $url = "http://localhost:8081/realms/Translation/protocol/openid-connect/token";

    $data = [
        "grant_type" => "client_credentials",
        "client_id" => "translation-api",
        "client_secret" => "6puAJn0myKkCcUQjUiFkUDGmTsA2ntua"
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded"
        ]
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        die("❌ CURL ERROR: " . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "<pre>HTTP CODE: $httpCode\n$response</pre>";

    $json = json_decode($response, true);

    if (!isset($json["access_token"])) {
        die("❌ ACCESS TOKEN NOT FOUND");
    }

    return $json["access_token"];
}
