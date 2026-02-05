<?php
require 'api.php';

$sid = $_GET['sid'];
$langId = $_GET['langId'];

[$status] = apiRequest(
    "DELETE",
    "http://localhost:5294/api/translations/$sid/$langId"
);

header("Location: index.php");
exit;
