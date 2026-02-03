<?php
require 'auth.php';   // must expose getAccessToken()

$result = null;
$httpCode = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sid    = $_POST['sid'] ?? '';
    $langId = $_POST['langId'] ?? '';

    if (!$sid || !$langId) {
        die("❌ SID and LangId are required");
    }

    $token = getAccessToken();
    if (!$token) {
        die("❌ Failed to get access token");
    }

    $url = "http://localhost:5294/api/translations/$sid/$langId";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . trim($token),
            "Accept: application/json"
        ]
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
    <title>Delete Translation</title>
</head>
<body>

<h2>Delete Translation</h2>

<form method="post">
    SID:<br>
    <input name="sid" value="003" required><br><br>

    Language:<br>
    <input name="langId" value="en" required><br><br>

    <button type="submit" style="color:red;">Delete</button>
</form>

<?php if ($httpCode !== null): ?>
    <h3>HTTP Status: <?= $httpCode ?></h3>
    <pre><?php print_r($result); ?></pre>
<?php endif; ?>

</body>
</html>
