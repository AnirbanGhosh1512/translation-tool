<?php
session_start();
?>

<h1>Translation Tool</h1>

<?php if (!isset($_SESSION["access_token"])): ?>
    <a href="login.php">Login</a>
<?php else: ?>
    <a href="api.php">Load translations</a><br><br>
    <a href="logout.php">Logout</a>
<?php endif; ?>
