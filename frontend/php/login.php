<?php
xdebug_break();

session_start();

/**docker stop $(docker ps -q)

 * CONFIG
 */
$keycloakBaseUrl = "http://localhost:8081";
$realm           = "Translation";
$clientId        = "translation-client";
$redirectUri     = "http://localhost:8000/callback.php";

/**
 * If already logged in, go directly to dashboard
 */
/**if (isset($_SESSION['access_token'])) {
    header("Location: index.php");
    exit;
}
 */

/**
 * Build Authorization URL
 */
$authUrl = $keycloakBaseUrl . "/realms/$realm/protocol/openid-connect/auth?" . http_build_query([
    "client_id"     => $clientId,
    "response_type" => "code",
    "scope"         => "openid profile email",
    "redirect_uri"  => $redirectUri
]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Translation Tool</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            width: 360px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        h1 {
            margin-bottom: 10px;
        }
        p {
            color: #555;
        }
        a.button {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: #5a67d8;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        a.button:hover {
            background: #434190;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h1>🌍 Translation Tool</h1>
    <p>Sign in using OpenID Connect</p>

    <a class="button" href="<?= htmlspecialchars($authUrl) ?>">
        Login with Keycloak
    </a>
</div>

</body>
</html>
