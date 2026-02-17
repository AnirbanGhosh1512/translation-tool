<?php
session_start();
header('Content-Type: application/json');

// --- check access token ---
if (!isset($_SESSION['access_token'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// --- read JSON from JS ---
$input = json_decode(file_get_contents('php://input'), true);
$sid = $input['sid'] ?? '';
$langId = $input['langId'] ?? '';
$text = $input['text'] ?? '';

if (!$sid || !$langId || $text === null) {
    echo json_encode(['success' => false, 'error' => 'Missing SID, langId, or text']);
    exit;
}

// --- prepare API call ---
$apiUrl = "http://translation-api:8080/api/translations/{$sid}/{$langId}";

// --- payload matches API requirements exactly ---
$payload = [
    'sid' => $sid,
    'langId' => $langId,
    'text' => $text
];

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $_SESSION['access_token'],
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// --- return response ---
if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode([
        'success' => true,
        'sid' => $sid,
        'langId' => $langId,
        'text' => $text
    ]);
} else {
    error_log("API PUT failed: HTTP $httpCode - Response: $response");
    echo json_encode(['success' => false, 'error' => $response ?: "HTTP $httpCode"]);
}
