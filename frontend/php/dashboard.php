<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header("Location: login.php");
    exit;
}

$parts = explode('.', $_SESSION['access_token']);
$payload = json_decode(base64_decode($parts[1]), true);

$apiUrl = "http://localhost:5294/api/translations";

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $_SESSION['access_token'],
        "Accept: application/json"
    ]
]);

$response = curl_exec($ch);

if ($response === false) {
    die("cURL error: " . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$rows = json_decode($response, true);

if ($httpCode !== 200 || !is_array($rows)) {
    echo "<h3>API Error</h3>";
    echo "<pre>";
    echo "HTTP Code: $httpCode\n";
    echo htmlspecialchars($response);
    echo "</pre>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Translation Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .table thead th {
            background-color: #f1f3f5;
            font-weight: 600;
        }
        .actions {
            white-space: nowrap;
        }
    </style>
</head>
<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📊 Translation Dashboard</h3>
        <span class="text-muted">
            Logged in as <strong><?= htmlspecialchars($payload['preferred_username'] ?? '') ?></strong>
        </span>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 100px;">SID</th>
                        <th style="width: 100px;">Lang</th>
                        <th>Text</th>
                        <th class="text-end" style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['sid']) ?></td>
                        <td><?= htmlspecialchars($row['langId']) ?></td>
                        <td><?= htmlspecialchars($row['text']) ?></td>
                        <td class="text-end actions">
                            <a href="edit.php?sid=<?= urlencode($row['sid']) ?>&langId=<?= urlencode($row['langId']) ?>"
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>
                            <a href="delete.php?sid=<?= urlencode($row['sid']) ?>&langId=<?= urlencode($row['langId']) ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this translation?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
