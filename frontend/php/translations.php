<?php
require_once "api.php";

function getTranslations() {
    return apiRequest("GET", "/api/translations");
}

function createTranslation($sid, $langId, $text) {
    return apiRequest("POST", "/api/translations", [
        "sid" => $sid,
        "langId" => $langId,
        "text" => $text
    ]);
}

function deleteTranslation($sid, $langId) {
    return apiRequest("DELETE", "/api/translations/$sid/$langId");
}
