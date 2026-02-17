<?php
session_start();

/**
 * CONFIG
 */
require __DIR__ . '/../config/config.php';

// Public URL (used for redirect in login.php)
$keycloakPublicUrl =  KEYCLOAK_PUBLIC_URL; 

// Internal Docker URL (used for server-to-server calls)
$keycloakInternalUrl = KEYCLOAK_INTERNAL_URL;

$realm        = KEYCLOAK_REALM;
$clientId     = KEYCLOAK_CLIENT_ID;
$clientSecret = KEYCLOAK_CLIENT_SECRET; 
$redirectUri  = CALLBACK_URL; 

/**
 * 1️⃣ Validate authorization code
 */
if (!isset($_GET['code'])) {
    http_response_code(400);
    die("Authorization code missing");
}

$code = $_GET['code'];

/**
 * 2️⃣ Token endpoint (IMPORTANT CHANGE HERE)
 */
$tokenUrl = "{$keycloakInternalUrl}/realms/{$realm}/protocol/openid-connect/token";

/**
 * 3️⃣ Token request payload
 */
$postData = [
    "grant_type"     => "authorization_code",
    "client_id"      => $clientId,
    "client_secret"  => $clientSecret,
    "code"           => $code,
    "redirect_uri"   => $redirectUri
];

/**
 * 4️⃣ Call Keycloak
 */
$ch = curl_init($tokenUrl);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ["Content-Type: application/x-www-form-urlencoded"],
    CURLOPT_POSTFIELDS     => http_build_query($postData),
    CURLOPT_TIMEOUT        => 10
]);

$response = curl_exec($ch);

if ($response === false) {
    die("cURL error: " . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

/**
 * 5️⃣ Decode response
 */
$data = json_decode($response, true);

/**
 * 6️⃣ Hard validation
 */
if ($httpCode !== 200 || !isset($data['access_token'])) {
    echo "<pre>";
    echo "HTTP Code: {$httpCode}\n";
    echo "Response:\n";
    print_r($data);
    echo "</pre>";
    exit;
}

/**
 * 7️⃣ Store session data
 */
$_SESSION['authenticated'] = true;
$_SESSION['access_token']  = $data['access_token'];
$_SESSION['refresh_token'] = $data['refresh_token'] ?? null;
$_SESSION['id_token']      = $data['id_token'] ?? null;

/**
 * 8️⃣ Redirect to dashboard
 */
header("Location: dashboard.php");
exit;
