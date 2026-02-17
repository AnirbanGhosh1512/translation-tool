<?php
session_start();

/**
 * Entry point

if (isset($_SESSION['access_token'])) {
    // User already authenticated
    header("Location: dashboard.php");
    exit;
}
     */

// Not authenticated → login
header("Location: login.php");
exit;
