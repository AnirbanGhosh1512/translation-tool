<?php
require 'api.php';

$msg = null;

if ($_POST) {
    [$status] = apiRequest(
        "POST",
        "http://localhost:5294/api/translations",
        [
            "sid" => $_POST['sid'],
            "langId" => $_POST['langId'],
            "text" => $_POST['text']
        ]
    );

    if ($status === 201) {
        header("Location: index.php");
        exit;
    }

    $msg = "Create failed";
}
?>

<h2>Create Translation</h2>

<form method="post">
SID: <input name="sid" required><br><br>
Lang: <input name="langId" value="en"><br><br>
Text: <input name="text" required><br><br>
<button>Create</button>
</form>

<p style="color:red"><?= $msg ?></p>
