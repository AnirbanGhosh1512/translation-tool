<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header("Location: login.php");
    exit;
}

$data = [
    "sid"    => $_POST['sid'],
    "langId" => $_POST['langId'],
    "text"   => $_POST['text']
];

$ch = curl_init("http://localhost:5294/api/translations");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $_SESSION['access_token'],
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 201 && $httpCode !== 200) {
    die("Create failed: " . htmlspecialchars($response));
}

header("Location: dashboard.php");
