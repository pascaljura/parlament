<?php
include '../assets/php/config.php';
session_start();
ob_start();

// Parametr z URL – podporujeme idusers_parlament i iduser_parlament (pro jistotu)
$targetId = 0;
if (isset($_GET['idusers_parlament'])) {
    $targetId = (int) $_GET['idusers_parlament'];
} elseif (isset($_GET['iduser_parlament'])) {
    $targetId = (int) $_GET['iduser_parlament'];
}

if (!$targetId) {
    http_response_code(400);
    echo "<p>Chybí parametr idusers_parlament.</p>";
    exit();
}

// Načtení přihlášeného uživatele a kontrola oprávnění
$admin = '0';
if (isset($_SESSION['idusers_parlament'])) {
    $me = (int) $_SESSION['idusers_parlament'];
    $stmt = $conn->prepare("SELECT admin, username, email FROM users_alba_rosa_parlament WHERE idusers_parlament = ?");
    $stmt->bind_param("i", $me);
    $stmt->execute();
    $meRes = $stmt->get_result();
    if ($meRow = $meRes->fetch_assoc()) {
        $admin = $meRow['admin'];
        $username_parlament = $meRow['username'];
        $email_parlament = $meRow['email'];
    }
    $stmt->close();
}
if ($admin !== '1') {
    echo '<!DOCTYPE html><html lang="cs"><head><meta charset="UTF-8"><title>Detail</title></head><body><div style="padding:14px;font-family:sans-serif">Chybí oprávnění.</div></body></html>';
    exit();
}

if (!function_exists('roleColors')) {
    function roleColors(string $role): array
    {
        if ($role === '') {
            return ['#f3f4f6', '#e5e7eb', '#111827'];
        }
        $source = function_exists('mb_strtolower') ? mb_strtolower($role, 'UTF-8') : strtolower($role);
        $hash = crc32($source);
        $golden = 0.61803398875;
        $base = fmod(($hash * $golden) * 360.0, 360.0);
        $STEP_DEG = 20;
        $bucket = (int) round($base / $STEP_DEG);
        $h = ($bucket * $STEP_DEG) % 360;
        $satChoices = [70, 78, 85, 90];
        $lightBg = [92, 94, 96];
        $s = $satChoices[$hash & 3];
        $lBg = $lightBg[($hash >> 2) % 3];
        $lBd = max(65, $lBg - 18);
        $sBd = min(95, $s + 6);
        $tx = '#0f172a';
        $bg = "hsl($h, {$s}%, {$lBg}%)";
        $bd = "hsl($h, {$sBd}%, {$lBd}%)";
        return [$bg, $bd, $tx];
    }
}

// Flash redirect zpět do detailu
function redirectDetail($id, $message, $type = 'info-message')
{
    $message = urlencode($message);
    header("Location: ./detail_user_parlament.php?idusers_parlament=$id&message=$message&message_type=$type");
    exit();
}

// Akce z formulářů na detailu
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'set_role') {
        $id = (int) ($_POST['idusers_parlament'] ?? 0);
        $role = trim($_POST['role'] ?? '');
        $role = preg_replace('/\s+/u', ' ', $role);
        if (mb_strlen($role) > 50)
            $role = mb_substr($role, 0, 50);

        $stmt = $conn->prepare("UPDATE users_alba_rosa_parlament SET role = ? WHERE idusers_parlament = ?");
        if ($stmt) {
            $stmt->bind_param("si", $role, $id);
            if ($stmt->execute())
                redirectDetail($id, "Role uložena.", "success-message");
            else
                redirectDetail($id, "Nepodařilo se uložit roli.", "error-message");
        } else
            redirectDetail($id, "Chyba v dotazu.", "error-message");
    }

    if ($action === 'add') {
        $id = (int) ($_POST['idusers_parlament'] ?? 0);
        $section = $_POST['section'] ?? '';
        $notes = $_POST['notes'] ?? '';
        if ($section !== '') {
            $stmt = $conn->prepare("INSERT INTO actions_alba_rosa_parlament (idusers_parlament, section, notes) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iss", $id, $section, $notes);
                if ($stmt->execute())
                    redirectDetail($id, "Záznam byl úspěšně přidán.", "success-message");
                else
                    redirectDetail($id, "Nepodařilo se přidat záznam.", "error-message");
            } else
                redirectDetail($id, "Chyba v dotazu.", "error-message");
        } else
            redirectDetail($id, "Musíte vybrat sekci.", "info-message");
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['idusers_parlament'] ?? 0);
        $delete_id = (int) ($_POST['idactions_parlament'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM actions_alba_rosa_parlament WHERE idactions_parlament = ?");
        if ($stmt) {
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute())
                redirectDetail($id, "Záznam byl úspěšně odstraněn.", "success-message");
            else
                redirectDetail($id, "Chyba při mazání záznamu.", "error-message");
        } else
            redirectDetail($id, "Chyba v dotazu.", "error-message");
    }

    if ($action === 'add_class') {
        $id = (int) ($_POST['idusers_parlament'] ?? 0);
        $school_year_raw = trim($_POST['school_year'] ?? '');
        $class_name = trim($_POST['class_name'] ?? '');
        if (!preg_match('/^\\d{4}\\s*\\/\\s*\\d{4}$/', $school_year_raw)) {
            redirectDetail($id, "Zadejte školní rok ve formátu RRRR/RRRR (např. 2024/2025).", "info-message");
        }
        list($startY, $endY) = preg_split('/\\s*\\/\\s*/', $school_year_raw);
        $startY = (int) $startY;
        $endY = (int) $endY;
        if ($endY !== $startY + 1 || $startY < 2000 || $startY > 2100) {
            redirectDetail($id, "Neplatný školní rok. Zadejte např. 2024/2025.", "info-message");
        }
        if ($id > 0 && $class_name !== '') {
            if (mb_strlen($class_name) > 50)
                $class_name = mb_substr($class_name, 0, 50);
            $stmt = $conn->prepare("INSERT INTO classes_alba_rosa_parlament (idusers_parlament, class_year, class_name) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iis", $id, $startY, $class_name);
                if ($stmt->execute())
                    redirectDetail($id, "Třída přidána.", "success-message");
                else
                    redirectDetail($id, "Nepodařilo se přidat třídu.", "error-message");
            } else
                redirectDetail($id, "Chyba v dotazu.", "error-message");
        } else
            redirectDetail($id, "Vyplňte název třídy.", "info-message");
    }

    if ($action === 'delete_class') {
        $id = (int) ($_POST['idusers_parlament'] ?? 0);
        $delete_id = (int) ($_POST['idclass_parlament'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM classes_alba_rosa_parlament WHERE idclass_parlament = ?");
        if ($stmt) {
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute())
                redirectDetail($id, "Třída byla odstraněna.", "success-message");
            else
                redirectDetail($id, "Chyba při mazání třídy.", "error-message");
        } else
            redirectDetail($id, "Chyba v dotazu.", "error-message");
    }
}

// Načti cílového uživatele
$stmt = $conn->prepare("SELECT * FROM users_alba_rosa_parlament WHERE idusers_parlament = ?");
$stmt->bind_param("i", $targetId);
$stmt->execute();
$r = $stmt->get_result();
if (!$user = $r->fetch_assoc()) {
    echo "<p>Uživatel nenalezen.</p>";
    exit();
}
$stmt->close();

$uid = (int) $user['idusers_parlament'];
$currentRole = $user['role'] ?? '';
$Name = $user['name'] ?? '';
$lastName = $user['last_name'] ?? '';
$email = $user['email'] ?? '';
$fullName = trim($Name . ' ' . $lastName);
list($roleBg, $roleBd, $roleTx) = roleColors($currentRole);

// Souhrny (KPI)
$counts = ['Účast na akci' => 0, 'Organizátor akce' => 0, 'Focení akce' => 0, 'Výbor' => 0];
if ($stmtC = $conn->prepare("SELECT section, COUNT(*) AS c FROM actions_alba_rosa_parlament WHERE idusers_parlament = ? GROUP BY section")) {
    $stmtC->bind_param("i", $uid);
    $stmtC->execute();
    $rC = $stmtC->get_result();
    while ($rc = $rC->fetch_assoc()) {
        $counts[$rc['section']] = (int) $rc['c'];
    }
    $stmtC->close();
}

// Třídy
$classes = [];
if ($stc = $conn->prepare("SELECT idclass_parlament, class_year, class_name FROM classes_alba_rosa_parlament WHERE idusers_parlament = ? ORDER BY class_year DESC, idclass_parlament DESC")) {
    $stc->bind_param("i", $uid);
    $stc->execute();
    $rc = $stc->get_result();
    while ($c = $rc->fetch_assoc()) {
        $classes[] = $c;
    }
    $stc->close();
}

// Záznamy (vše)
$notes = [];
$q = $conn->prepare("SELECT idactions_parlament, section, notes FROM actions_alba_rosa_parlament WHERE idusers_parlament = ? ORDER BY idactions_parlament DESC");
$q->bind_param("i", $uid);
$q->execute();
$qr = $q->get_result();
while ($row = $qr->fetch_assoc()) {
    $notes[] = $row;
}
$q->close();
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Parlament na Purkyňce</title>

    <link rel="manifest" href="../assets/json/manifest.json">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <meta property="og:title" content="Parlament na Purkyňce" />
    <meta property="og:url" content="https://www.alba-rosa.cz/parlament/" />
    <meta property="og:image" content="https://www.alba-rosa.cz/parlament/logo.png" />
    <meta property="og:description"
        content="Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a podílejí se na chodu školy." />
    <meta name="theme-color" content="#5481aa" />

    <style>
        :root {
            --border: #e6e8ee;
            --chip: #f3f4f6;
            --text: #0f172a;
            --brand: #5481aa;
            --danger: #ef4444;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--text);
            background: #f7f9fc;
        }

        .wrap {
            padding: 14px;
            display: grid;
            gap: 12px;
        }

        .head {
            background: linear-gradient(180deg, #355170, #2c425a);
            color: #fff;
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .title {
            font-weight: 700;
        }

        .email {
            opacity: .9;
        }

        .kpis-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .kpi {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--chip);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 13px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 12px;
        }

        @media (max-width: 820px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
        }

        .card h4 {
            margin: 0 0 8px;
        }

        .select,
        textarea,
        input[type="text"] {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
            width: 100%;
            background: #fff;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .btn {
            border: none;
            background: var(--brand);
            color: #fff;
            padding: 8px 12px;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn:hover {
            filter: brightness(1.05);
        }

        .btn.icon {
            padding: 6px 8px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn.danger {
            background: var(--danger);
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--chip);
            font-size: 12px;
        }

        /* Vodorovný seznam karet (záznamy) */
        .horizontal-cards {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 6px;
            scroll-snap-type: x mandatory;
        }

        .horizontal-cards .note {
            min-width: 280px;
            max-width: 360px;
            flex: 0 0 auto;
            scroll-snap-align: start;
        }

        .note {
            position: relative;
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: flex-start;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
        }

        .note .left {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .badge._ucast {
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        .badge._org {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .badge._foto {
            background: #fdf4ff;
            border-color: #f5d0fe;
        }

        .badge._vybor {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .class-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            margin: 6px 0;
        }

        .class-row .txt .year {
            font-weight: 700;
        }

        .role-badge {
            font-weight: 600;
            letter-spacing: .2px;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid
                <?php echo htmlspecialchars($roleBd); ?>
            ;
            background:
                <?php echo htmlspecialchars($roleBg); ?>
            ;
            color:
                <?php echo htmlspecialchars($roleTx); ?>
            ;
        }
    </style>
</head>

<body>
    <div id="calendar">
        <div class="wrap">
            <?php
            if (isset($_GET['message']) && isset($_GET['message_type'])) {
                $message = $_GET['message'];
                $message_type = $_GET['message_type'];
                $cls = 'info-message';
                if ($message_type === 'success-message')
                    $cls = 'success-message';
                elseif ($message_type === 'error-message')
                    $cls = 'error-message';
                echo '<div class="' . $cls . '" style="background:#eef6ff;border:1px solid #cfe3ff;padding:10px;border-radius:10px;">' . htmlspecialchars_decode($message) . '</div>';
            }
            ?>
            <div class="head">
                <div class="title"><i class="fa fa-user"></i> <?php echo htmlspecialchars($fullName); ?> <span
                        class="email">• <?php echo htmlspecialchars($email); ?></span></div>
                <span
                    class="role-badge"><?php echo $currentRole !== '' ? htmlspecialchars($currentRole) : '— bez role —'; ?></span>
            </div>

            <div class="kpis-row">
                <span class="kpi"><i class="fa fa-users"></i> Účast:
                    <?php echo (int) ($counts['Účast na akci'] ?? 0); ?></span>
                <span class="kpi"><i class="fa fa-cogs"></i> Organizátor:
                    <?php echo (int) ($counts['Organizátor akce'] ?? 0); ?></span>
                <span class="kpi"><i class="fa fa-camera"></i> Focení:
                    <?php echo (int) ($counts['Focení akce'] ?? 0); ?></span>
                <span class="kpi"><i class="fa fa-university"></i> Výbor:
                    <?php echo (int) ($counts['Výbor'] ?? 0); ?></span>
            </div>

            <div class="grid">
                <div class="card">
                    <h4><i class="fa fa-id-badge"></i> Role</h4>
                    <form method="POST" class="form-row"
                        style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                        <input type="hidden" name="action" value="set_role">
                        <input type="hidden" name="idusers_parlament" value="<?php echo $uid; ?>">
                        <input class="select" name="role" list="roles-list"
                            value="<?php echo htmlspecialchars($currentRole); ?>"
                            placeholder="Napište roli nebo vyberte…" aria-label="Zvolte nebo napište roli"
                            style="flex:1;min-width:220px;" />
                        <datalist id="roles-list">
                            <option value="Člen">
                            <option value="Vedoucí">
                            <option value="Místopředseda">
                            <option value="Organizátor">
                            <option value="Fotograf">
                            <option value="Host">
                        </datalist>
                        <button class="btn" type="submit"><i class="fa fa-save"></i> Uložit roli</button>
                    </form>
                </div>

                <div class="card">
                    <h4><i class="fa fa-graduation-cap"></i> Třídy</h4>
                    <form method="POST" class="form-row"
                        style="align-items:flex-end; gap:10px; display:flex; flex-wrap:wrap;">
                        <input type="hidden" name="action" value="add_class">
                        <input type="hidden" name="idusers_parlament" value="<?php echo $uid; ?>">

                        <div style="min-width:160px">
                            <label for="school_year" style="font-weight:600">Školní rok</label>
                            <input id="school_year" class="select school-year" type="text" name="school_year"
                                inputmode="numeric" placeholder="2024/2025" pattern="\\d{4}\\s*/\\s*\\d{4}"
                                title="Zadejte ve formátu 2024/2025" required>
                        </div>

                        <div style="min-width:160px; flex:1">
                            <label for="class_name" style="font-weight:600">Třída</label>
                            <input id="class_name" class="select" type="text" name="class_name" maxlength="50"
                                placeholder="např. 9.A" required>
                        </div>

                        <button class="btn" type="submit"><i class="fa fa-plus"></i> Přidat třídu</button>
                    </form>

                    <div style="margin-top:10px">
                        <?php
                        if (empty($classes)) {
                            echo '<div style="opacity:.8;"><em>Žádné záznamy tříd</em></div>';
                        } else {
                            foreach ($classes as $c) {
                                $y = (int) $c['class_year'];
                                echo '<div class="class-row">
                                    <div class="txt"><span class="year">' . ($y) . '/' . ($y + 1) . '</span> – ' . htmlspecialchars($c['class_name']) . '</div>
                                    <form method="POST" class="inline" onsubmit="if(!confirm(\'Opravdu odstranit třídu?\')){event.preventDefault();}">
                                        <input type="hidden" name="action" value="delete_class">
                                        <input type="hidden" name="idusers_parlament" value="' . $uid . '">
                                        <input type="hidden" name="idclass_parlament" value="' . (int) $c['idclass_parlament'] . '">
                                        <button class="btn icon danger" type="submit" title="Odstranit třídu"><i class="fa fa-trash"></i></button>
                                    </form>
                                  </div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="card" style="grid-column: 1 / -1;">
                    <h4><i class="fa fa-plus-circle"></i> Přidat záznam</h4>
                    <form method="POST" class="form-row" style="align-items:flex-end">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="idusers_parlament" value="<?php echo $uid; ?>">
                        <select class="select" name="section" required style="min-width:220px">
                            <option value="" disabled selected>-- Vyberte sekci --</option>
                            <option value="Účast na akci">Účast na akci</option>
                            <option value="Organizátor akce">Organizátor akce</option>
                            <option value="Focení akce">Focení akce</option>
                            <option value="Výbor">Výbor</option>
                        </select>
                        <textarea name="notes" placeholder="Poznámka..." required></textarea>
                        <button class="btn" type="submit"><i class="fa fa-plus"></i> Přidat</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <h4><i class="fa fa-list-ul"></i> Záznamy</h4>
                <div class="horizontal-cards">
                    <?php
                    if (empty($notes)) {
                        echo '<div style="padding:6px 10px; opacity:.8;"><em>Žádné záznamy</em></div>';
                    } else {
                        foreach ($notes as $row) {
                            $sec = $row['section'];
                            $cls = '_ucast';
                            if ($sec === 'Organizátor akce')
                                $cls = '_org';
                            elseif ($sec === 'Focení akce')
                                $cls = '_foto';
                            elseif ($sec === 'Výbor')
                                $cls = '_vybor';
                            echo '<div class="note">
                                <div class="left">
                                    <span class="badge ' . $cls . '">' . htmlspecialchars($sec) . '</span>
                                    <div>' . nl2br(htmlspecialchars($row['notes'])) . '</div>
                                </div>
                                <form method="POST" class="inline" onsubmit="if(!confirm(\'Opravdu chcete tento záznam odstranit?\')){event.preventDefault();}">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="idusers_parlament" value="' . $uid . '">
                                    <input type="hidden" name="idactions_parlament" value="' . (int) $row['idactions_parlament'] . '">
                                    <button class="btn icon danger" type="submit" title="Odstranit"><i class="fa fa-trash"></i></button>
                                </form>
                              </div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>