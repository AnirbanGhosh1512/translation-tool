<?php
function getToken() {
    $url = "http://localhost:8081/realms/translation/protocol/openid-connect/token";

    $data = [
        "client_id" => "php-client",
        "client_secret" => "php-secret",
        "grant_type" => "client_credentials"
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true)["access_token"];
}

$result = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = getToken();

    $payload = json_encode([
        "sid"    => $_POST["sid"],
        "langId" => $_POST["langId"],
        "text"   => $_POST["text"]
    ]);

    $ch = curl_init("http://localhost:5000/api/translations");
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $result = curl_exec($ch);
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
    SID (existing): <input name="sid" required><br><br>
    Language: <input name="langId" value="en" required><br><br>
    New Text: <input name="text" required><br><br>
    <button>Update</button>
</form>

<?php if ($result !== null): ?>
<pre><?php echo $result === "" ? "Updated successfully (204)" : htmlspecialchars($result); ?></pre>
<?php endif; ?>

</body>
</html>
