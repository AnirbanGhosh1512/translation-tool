<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header("Location: login.php");
    exit;
}

$parts = explode('.', $_SESSION['access_token']);
$payload = json_decode(base64_decode($parts[1]), true);

$isTranslator = false;
if (isset($payload['realm_access']['roles'])) {
    $isTranslator = in_array('translator', $payload['realm_access']['roles'], true);
}

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
        * {
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont;
            background: #f1f5f9;
            margin: 0;
            padding: 40px;
            color: #1f2937;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8fafc;
        }

        th {
            text-align: left;
            padding: 14px 16px;
            font-size: 14px;
            color: #475569;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 15px;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            transition: transform .05s ease, opacity .15s ease;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .edit {
            background: #2563eb;
        }

        .delete {
            background: #dc2626;
        }

        .btn.primary {
            background: #16a34a;
        }

        .btn.secondary {
            background: #64748b;
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            width: 420px;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .2);
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content label {
            display: block;
            margin-top: 14px;
            font-weight: 600;
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .modal-content textarea {
            resize: vertical;
        }

        .modal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn.primary {
            background: #16a34a;
        }

        .btn.secondary {
            background: #64748b;
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            width: 420px;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .2);
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content label {
            display: block;
            margin-top: 14px;
            font-weight: 600;
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .modal-content textarea {
            resize: vertical;
        }

        .modal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            width: 420px;
            border-radius: 10px;
        }

        .modal textarea {
            width: 100%;
            height: 100px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }

        .save {
            background: #3182ce;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
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

        <button class="btn primary" onclick="openModal()">➕ Add Translation</button>

        <div class="card">
            <div class="card-body">

                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">SID</th>
                            <!--<th style="width: 100px;">Lang</th>-->
                            <th>Text</th>
                            <!--<th class="text-center" style="width: 150px;">Actions</th>-->
                        </tr>
                    </thead>
                    <tbody>

                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr ondblclick="openEdit(
                                '<?= htmlspecialchars($row['sid'], ENT_QUOTES) ?>',
                                `<?= htmlspecialchars($row['text'], ENT_QUOTES) ?>`
                            )">
                                <td><?= htmlspecialchars($row['sid']) ?></td>
                                <td><?= htmlspecialchars($row['text']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <h2>Add Translation</h2>

            <form method="post" action="create.php">
                <label>SID</label>
                <input type="text" name="sid" required>

                <label>Language</label>
                <input type="text" name="langId" required>

                <label>Text</label>
                <textarea name="text" required></textarea>

                <div class="modal-actions">
                    <button type="submit" class="btn primary">Save</button>
                    <button type="button" class="btn secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Translation</h3>

            <input type="hidden" id="editSid">
            <input type="hidden" id="editLang">

            <label>Text</label>
            <textarea id="editText"></textarea>

            <div class="modal-actions">
                <button type="button" onclick="closeModal()">Cancel</button>
                <button type="button" class="save" onclick="saveEdit()">Save</button>
            </div>
        </div>
    </div>
    <script>
        function openModal() {
            document.getElementById('modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>

    <script>
        function openEdit(sid, lang, text) {
            document.getElementById('editSid').value = sid;
            document.getElementById('editLang').value = lang;
            document.getElementById('editText').value = text;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function saveEdit() {
            const sid = document.getElementById('editSid').value;
            const lang = document.getElementById('editLang').value;
            const text = document.getElementById('editText').value;

            fetch(`http://localhost:5294/api/translations/${sid}/${lang}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer <?= $_SESSION['access_token'] ?>',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        sid: sid,
                        langId: lang,
                        text: text
                    })
                })
                .then(async res => {
                    if (!res.ok) {
                        const text = await res.text();
                        throw new Error(`HTTP ${res.status}: ${text}`);
                    }
                    closeModal();
                    location.reload();
                })
                .catch(err => {
                    alert("Save failed:\n" + err.message);
                    console.error("PUT error:", err);
                });
        }
    </script>

    <script>
        function deleteTranslation(sid, lang) {
            if (!confirm("Delete this translation?")) return;

            fetch(`http://localhost:5294/api/translations/${sid}/${lang}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer <?= $_SESSION['access_token'] ?>'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error("Delete failed");
                    location.reload();
                })
                .catch(err => {
                    alert("Delete failed");
                    console.error(err);
                });
        }
    </script>

</body>

</html>