<?php
require 'auth.php';   // must expose getAccessToken()

$result = null;
$httpCode = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sid    = $_POST['sid'] ?? '';
    $langId = $_POST['langId'] ?? '';
    $text   = $_POST['text'] ?? '';

    if (!$sid || !$langId || !$text) {
        die("❌ SID, LangId and Text are required");
    }

    $token = getAccessToken();
    if (!$token) {
        die("❌ Failed to get access token");
    }

    $payload = json_encode([
        "sid"    => $sid,
        "langId" => $langId,
        "text"   => $text
    ]);

    $url = "http://localhost:5294/api/translations/$sid/$langId";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . trim($token),
            "Content-Type: application/json",
            "Accept: application/json"
        ],
        CURLOPT_POSTFIELDS => $payload
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $result = ["error" => curl_error($ch)];
    } else {
        $result = $response ? json_decode($response, true) : "No Content";
    }

    curl_close($ch);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Translation</title>
</head>
<body>

<h2>Update Translation</h2>

<form method="post">
    SID:<br>
    <input name="sid" value="003" required><br><br>

    Language:<br>
    <input name="langId" value="en" required><br><br>

    Text:<br>
    <input name="text" value="welcome test UPDATED via PHP" required><br><br>

    <button type="submit">Update</button>
</form>

<?php if ($httpCode !== null): ?>
    <h3>HTTP Status: <?= $httpCode ?></h3>
    <pre><?php print_r($result); ?></pre>
<?php endif; ?>

</body>
</html>
