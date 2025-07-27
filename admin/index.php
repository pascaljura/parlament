<?php
$mysqli = new mysqli("localhost", "root", "", "main");
$mysqli->set_charset("utf8");

// Zpracování přidání záznamu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id = (int) $_POST['idusers_parlament'];
    $section = $_POST['section'];
    $notes = $_POST['notes'];

    if (!empty($section)) {
        $stmt = $mysqli->prepare("INSERT INTO actions_alba_rosa_parlament (idusers_parlament, section, notes) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $section, $notes);
        $stmt->execute();
    }
}

// Zpracování odstranění záznamu
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $delete_id = (int) $_POST['idactions_parlament'];
    $mysqli->query("DELETE FROM actions_alba_rosa_parlament WHERE idactions_parlament = $delete_id");
}

// Načtení uživatelů
$users = $mysqli->query("SELECT * FROM users_alba_rosa_parlament ORDER BY last_name, name");
?>

<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Seznam uživatelů - Parlament</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        .user-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }

        .note-list {
            margin-top: 10px;
            background: #f9f9f9;
            padding: 10px;
        }

        .note-item {
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .note-content {
            flex: 1;
        }

        form.inline {
            display: inline;
            margin: 0;
        }

        button.delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
        }
    </style>
    <script>
        function confirmDelete(form) {
            if (confirm("Opravdu chcete tento záznam odstranit?")) {
                form.submit();
            }
        }
    </script>
</head>

<body>

    <h1>Seznam uživatelů parlamentu</h1>

    <?php while ($user = $users->fetch_assoc()): ?>
        <div class="user-box">
            <strong><?= htmlspecialchars($user['name'] . ' ' . $user['last_name']) ?></strong><br>
            <em><?= htmlspecialchars($user['email']) ?></em>

            <!-- Formulář pro přidání záznamu -->
            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="idusers_parlament" value="<?= $user['idusers_parlament'] ?>">
                <label>Sekce:</label>
                <select name="section" required>
                    <option value="">-- Vyber --</option>
                    <option value="Účast na akci">Účast na akci</option>
                    <option value="Organizátor akce">Organizátor akce</option>
                    <option value="Focení akce">Focení akce</option>
                    <option value="Výbor">Výbor</option>
                </select><br><br>

                <label>Poznámka:</label><br>
                <textarea name="notes" rows="2" cols="50" placeholder="Poznámka..."></textarea><br><br>

                <button type="submit">Přidat záznam</button>
            </form>

            <!-- Seznam akcí -->
            <div class="note-list">
                <strong>Záznamy:</strong>
                <?php
                $id = (int) $user['idusers_parlament'];
                $result = $mysqli->query("SELECT idactions_parlament, section, notes FROM actions_alba_rosa_parlament WHERE idusers_parlament = $id ORDER BY idactions_parlament DESC");
                if ($result->num_rows === 0): ?>
                    <p><em>Žádné záznamy</em></p>
                <?php else:
                    while ($row = $result->fetch_assoc()): ?>
                        <div class="note-item">
                            <div class="note-content">
                                <strong><?= htmlspecialchars($row['section']) ?>:</strong>
                                <?= nl2br(htmlspecialchars($row['notes'])) ?>
                            </div>
                            <form method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete(this);">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="idactions_parlament" value="<?= $row['idactions_parlament'] ?>">
                                <button class="delete-btn" type="submit">Odstranit</button>
                            </form>
                        </div>
                    <?php endwhile;
                endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

</body>

</html>