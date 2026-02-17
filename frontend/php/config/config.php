<?php
/**
 * Global Application Configuration
 * --------------------------------
 * Loads environment variables and defines application constants.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load .env file
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        $_ENV[$name] = trim($value);
    }
}



function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}


// =============================
// Keycloak Config
// =============================

define('APP_ENV', env('APP_ENV', 'development'));

define('KEYCLOAK_PUBLIC_URL', env('KEYCLOAK_PUBLIC_URL', 'http://localhost:8081'));
define('KEYCLOAK_INTERNAL_URL', env('KEYCLOAK_INTERNAL_URL', 'http://keycloak:8080'));

define('KEYCLOAK_REALM', env('KEYCLOAK_REALM', 'Translation'));
define('KEYCLOAK_CLIENT_ID', env('KEYCLOAK_CLIENT_ID', 'translation-client'));
define('KEYCLOAK_CLIENT_SECRET', env('KEYCLOAK_CLIENT_SECRET', ''));

define('API_BASE_URL', env('API_BASE_URL', 'http://translation-api:8080'));


/*
|--------------------------------------------------------------------------
| Application URLs
|--------------------------------------------------------------------------
*/

define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');

define('LOGIN_URL', APP_URL . '/login.php');
define('LOGOUT_URL', APP_URL . '/logout.php');
define('CALLBACK_URL', APP_URL . '/callback.php');
define('DASHBOARD_URL', APP_URL . '/dashboard.php');

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

/**
 * Redirect helper
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/**
 * Check authentication
 */
function requireAuth()
{
    if (!isset($_SESSION['access_token'])) {
        redirect(LOGIN_URL);
    }
}

/**
 * Get Authorization Header
 */
function getAuthHeader()
{
    if (!isset($_SESSION['access_token'])) {
        return null;
    }

    return "Authorization: Bearer " . $_SESSION['access_token'];
}
