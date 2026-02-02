<?php
require 'auth.php';

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = getAccessToken();

    $payload = json_encode([
        "sid"    => $_POST["sid"],
        "langId" => $_POST["langId"], // ✅ FIXED
        "text"   => $_POST["text"]
    ]);

    $ch = curl_init("http://localhost:5294/api/translations");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => $payload
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
}
?>

<h2>Create Translation</h2>

<form method="post">
    SID: <input name="sid" required><br><br>
    Language: <input name="langId" value="en" required><br><br>
    Text: <input name="text" required><br><br>
    <button>Create</button>
</form>

<?php if ($result): ?>
<pre><?php print_r($result); ?></pre>
<?php endif; ?>
