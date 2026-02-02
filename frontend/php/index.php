<?php
require_once "translations.php";

$translations = getTranslations();
?>

<h1>Translations</h1>

<ul>
<?php foreach ($translations as $t): ?>
    <li>
        <?= htmlspecialchars($t["sid"]) ?>
        (<?= htmlspecialchars($t["langId"]) ?>):
        <?= htmlspecialchars($t["text"]) ?>
    </li>
<?php endforeach; ?>
</ul>
