<?php
include '../assets/php/config.php';
session_start();
ob_start();

if (!function_exists('roleColors')) {
    function roleColors(string $role): array {
        if ($role === '') {
            // neutrální "bez role"
            return ['#f3f4f6', '#e5e7eb', '#111827'];
        }

        // stabilní hash z textu (diakritika nevadí)
        $source = function_exists('mb_strtolower') ? mb_strtolower($role, 'UTF-8') : strtolower($role);
        $hash   = crc32($source);

        // 1) rozprostření přes "zlatý úhel" + 2) kvantizace na kroky (větší rozestup odstínů)
        $golden = 0.61803398875;                                  // zlatý poměr (konjugát)
        $base   = fmod(($hash * $golden) * 360.0, 360.0);         // rozprostřený hue
        $STEP_DEG = 20;                                           // krok kvantizace (čím větší, tím víc rozdílné barvy)
        $bucket = (int) round($base / $STEP_DEG);
        $h = ($bucket * $STEP_DEG) % 360;

        // Jemné obměny saturace/světlosti, ale pořád pastel
        $satChoices = [70, 78, 85, 90];
        $lightBg    = [92, 94, 96];

        $s   = $satChoices[$hash & 3];
        $lBg = $lightBg[($hash >> 2) % 3];

        // okraj o něco tmavší a s mírně vyšší saturací
        $lBd = max(65, $lBg - 18);
        $sBd = min(95, $s + 6);

        // text necháme tmavý – na pastelu čitelný
        $tx  = '#0f172a';

        // Použijeme čárkovou syntaxi kvůli široké kompatibilitě
        $bg = "hsl($h, {$s}%, {$lBg}%)";
        $bd = "hsl($h, {$sBd}%, {$lBd}%)";

        return [$bg, $bd, $tx];
    }
}

if (isset($_SESSION['idusers_parlament'])) {
    $userId = $_SESSION['idusers_parlament'];

    $stmt = $conn->prepare("SELECT * FROM users_alba_rosa_parlament WHERE idusers_parlament = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($userData = $result->fetch_assoc()) {
        $idusers_parlament = $userData['idusers_parlament'];
        $email_parlament = $userData['email'];
        $username_parlament = $userData['username'];
        $parlament_access_admin = $userData['parlament_access_admin'];
        $parlament_access_user = $userData['parlament_access_user'];
        $add_notes = $userData['add_notes'];
        $delete_notes = $userData['delete_notes'];
        $edit_notes = $userData['edit_notes'];
        $start_attendances = $userData['start_attendances'];
        $end_attendances = $userData['end_attendances'];
        $delete_attendances = $userData['delete_attendances'];
        $qr_attendances = $userData['qr_attendances'];
        $select_idnotes_parlament = $userData['select_idnotes_parlament'];
        $show_attendances = $userData['show_attendances'];
        $admin = $userData['admin'];
    } else {
        header("Location: ./logout.php");
        exit();
    }
    $stmt->close();
}

// ---------- Flash redirect ----------
function redirectWithMessage($message, $type = 'info-message')
{
    $message = urlencode($message);
    header("Location: ./?message=$message&message_type=$type");
    exit();
}

// ---------- Add record ----------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id = (int) $_POST['idusers_parlament'];
    $section = $_POST['section'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (!empty($section)) {
        $stmt = $conn->prepare("INSERT INTO actions_alba_rosa_parlament (idusers_parlament, section, notes) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iss", $id, $section, $notes);
            if ($stmt->execute()) {
                redirectWithMessage("Záznam byl úspěšně přidán.", "success-message");
            } else {
                redirectWithMessage("Nepodařilo se přidat záznam.", "error-message");
            }
        } else {
            redirectWithMessage("Chyba v dotazu.", "error-message");
        }
    } else {
        redirectWithMessage("Musíte vybrat sekci.", "info-message");
    }
}

// ---------- Delete record ----------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $delete_id = (int) ($_POST['idactions_parlament'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM actions_alba_rosa_parlament WHERE idactions_parlament = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            redirectWithMessage("Záznam byl úspěšně odstraněn.", "success-message");
        } else {
            redirectWithMessage("Chyba při mazání záznamu.", "error-message");
        }
    } else {
        redirectWithMessage("Chyba v dotazu.", "error-message");
    }
}

// ---------- Set role ----------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'set_role') {
    $id = (int) ($_POST['idusers_parlament'] ?? 0);

    // dovolíme vlastní roli (free-text) + sanitizace a rozumné limity
    $role = trim($_POST['role'] ?? '');
    // normalizace whitespace
    $role = preg_replace('/\s+/u', ' ', $role);
    // oříznutí délky (match DB VARCHAR(50))
    if (mb_strlen($role) > 50) {
        $role = mb_substr($role, 0, 50);
    }

    // volitelné: můžeš nedovolit čistě prázdný řetězec, já ponechávám prázdné = „bez role“
    // if ($role === '') { redirectWithMessage("Role nesmí být prázdná.", "error-message"); }

    $stmt = $conn->prepare("UPDATE users_alba_rosa_parlament SET role = ? WHERE idusers_parlament = ?");
    if ($stmt) {
        $stmt->bind_param("si", $role, $id);
        if ($stmt->execute()) {
            redirectWithMessage("Role uložena.", "success-message");
        } else {
            redirectWithMessage("Nepodařilo se uložit roli.", "error-message");
        }
    } else {
        redirectWithMessage("Chyba v dotazu.", "error-message");
    }
}

// ---------- Load users ----------
$users = $conn->query("SELECT * FROM users_alba_rosa_parlament ORDER BY last_name, name");
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
            --brand: #5481aa;
            --brand-2: #77afe0;
            --border: #e6e8ee;
            --card: #fff;
            --text: #1f2937;
            --muted: #6b7280;
            --danger: #ef4444;
            --ok: #16a34a;
            --chip: #f3f4f6;
            --ucast: #10b981;
            /* zelená */
            --org: #3b82f6;
            /* modrá */
            --foto: #a855f7;
            /* fialová */
            --vybor: #f59e0b;
            /* oranžová */
        }

        body {
            background: #f6f8fb;
            color: var(--text);
        }

        .badge.role-badge {
            font-weight: 600;
            letter-spacing: .2px;
        }

        .table-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 12px 0 18px;
        }

        .table-heading h2 {
            margin: 0;
            font-family: "Roboto Slab", serif;
            font-size: clamp(22px, 2.4vw, 28px);
        }

        .table-heading .blue {
            color: var(--brand);
        }

        .table-wrap {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: auto;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
        }

        table.users {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 940px;
        }

        .users th,
        .users td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
        }

        .users thead th {
            position: sticky;
            top: 0;
            background: #5481aa;
            color: #fafafa;
            font-weight: 700;
        }

        .users tbody tr:hover {
            background: #fafafa;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            background: var(--chip);
            border: 1px solid var(--border);
            font-size: 12px;
        }

        .role {
            font-weight: 600;
            color: #0f172a;
        }

        .email {
            color: #fafafa;
        }

        /* Akce v tabulce */
        .acts {
            display: grid;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .act {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 10px;
        }

        .act .row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .act .note {
            color: #374151;
            font-size: 13px;
            margin: 6px 0 2px 0;
            line-height: 1.35;
        }

        .act._ucast {
            background: #ecfdf5;
            border-color: #bbf7d0;
            box-shadow: inset 3px 0 0 0 var(--ucast);
        }

        .act._org {
            background: #eff6ff;
            border-color: #bfdbfe;
            box-shadow: inset 3px 0 0 0 var(--org);
        }

        .act._foto {
            background: #fdf4ff;
            border-color: #f5d0fe;
            box-shadow: inset 3px 0 0 0 var(--foto);
        }

        .act._vybor {
            background: #fff7ed;
            border-color: #fed7aa;
            box-shadow: inset 3px 0 0 0 var(--vybor);
        }

        .btn {
            border: none;
            background: var(--brand);
            color: #fff;
            padding: 8px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: background .2s;
        }

        .btn:hover {
            background: var(--brand-2);
        }

        .btn.ghost {
            background: #fff;
            color: #355170;
        }

        .btn.danger {
            background: var(--danger);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .success-message,
        .error-message,
        .info-message {
            margin: 12px 0;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid;
            cursor: pointer;
        }

        .success-message {
            background: #ecfdf5;
            border-color: #bbf7d0;
            color: #065f46
        }

        .error-message {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b
        }

        .info-message {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af
        }

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, .45);
            z-index: 1000;
        }

        .modal.open {
            display: flex;
        }

        .modal-card {
            width: min(860px, 94vw);
            background: #fff;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 30px 60px rgba(0, 0, 0, .25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-head {
            padding: 14px 16px;
            background: #355170;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            color: #fafafa;
        }

        .modal-title {
            font-weight: 700;
        }

        .modal-body {
            padding: 16px;
            display: grid;
            gap: 14px;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 16px;
        }

        @media (max-width: 820px) {
            .modal-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
        }

        .card h4 {
            margin: 0 0 8px;
        }

        .form-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .select,
        textarea {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
            width: 100%;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .note {
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
            align-items: center;
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

        .kpis {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .kpis .chip {
            background: var(--chip);
            border: 1px solid var(--border);
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .users th.sortable {
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .users th.sortable:after {
            content: '\f0dc';
            /* fa-sort ikonka */
            font-family: FontAwesome;
            font-size: 12px;
            position: absolute;
            right: 8px;
            opacity: 0.5;
        }

        .users th.sortable.asc:after {
            content: '\f0de';
            /* fa-sort-up */
            opacity: 0.9;
        }

        .users th.sortable.desc:after {
            content: '\f0dd';
            /* fa-sort-down */
            opacity: 0.9;
        }
    </style>

    <script>
        function removeQueryString() {
            const url = new URL(window.location);
            url.searchParams.delete('message'); url.searchParams.delete('message_type');
            window.history.replaceState({}, '', url);
            const banners = document.querySelectorAll('.success-message, .error-message, .info-message');
            banners.forEach(b => b.style.display = 'none');
        }

        // Modal helpers
        function openDetail(uid) {
            const modal = document.getElementById('personModal');
            const slot = document.getElementById('modalContentSlot');
            const tpl = document.getElementById('tpl-' + uid);
            if (!tpl) { return; }
            slot.innerHTML = tpl.innerHTML;
            modal.classList.add('open');
            const first = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (first) first.focus();
        }
        function closeDetail() {
            const modal = document.getElementById('personModal');
            const slot = document.getElementById('modalContentSlot');
            modal.classList.remove('open');
            slot.innerHTML = '';
        }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDetail(); });
    </script>
</head>

<body>
    <div id="calendar">
        <nav>
            <div class="user-icon">
                <?php if (!empty($username_parlament)) { ?>
                    <i class="fa fa-user" style="color:#5481aa"></i>
                <?php } else { ?>
                    <i class="fa fa-user" style="color:#3C3C3B"></i>
                <?php } ?>
            </div>
            <div class="nav-links">
                <a href="../">Domů</a>
                <a href="../notes">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="../attendances">Prezenční listiny</a>
                <?php } ?>
                <?php if (isset($admin) && $admin == '1') { ?>
                    <a href="../admin" class="active">Admin</a>
                <?php } ?>
            </div>
        </nav>

        <div class="wrap">
            <?php
            if (isset($_GET['message']) && isset($_GET['message_type'])) {
                $message = $_GET['message'];
                $message_type = $_GET['message_type'];
                $cls = 'info-message';
                $icon = 'fa-info-circle';
                if ($message_type === 'success-message') {
                    $cls = 'success-message';
                    $icon = 'fa-check';
                } elseif ($message_type === 'error-message') {
                    $cls = 'error-message';
                    $icon = 'fa-times';
                }
                echo '<div onclick="removeQueryString()" class="' . $cls . '"><i class="fa ' . $icon . '" style="margin-right:5px;"></i> ' . htmlspecialchars_decode($message) . '</div>';
            }
            ?>

            <?php if (isset($admin) && $admin == '1') { ?>
                <div class="table-heading">
                    <h2><i class="fa fa-heart blue"></i>・Seznam uživatelů parlamentu</h2>
                </div>

                <div class="table-wrap">
                    <table class="users">
                        <thead>
                            <tr>
                                <th class="sortable">Jméno</th>
                                <th class="sortable">Přijmení</th>
                                <th class="sortable">E-mail</th>
                                <th class="sortable">Role</th>
                                <th>Akce (posledních 5)</th>
                                <th>Detail</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <?php
                                $uid = (int) $user['idusers_parlament'];
                                $currentRole = $user['role'] ?? '';
                                $Name = ($user['name'] ?? '');
                                $lastName = ($user['last_name'] ?? '');
                                $email = $user['email'] ?? '';
                                $fullName = trim($Name . ' ' . $lastName);
                                list($roleBg, $roleBd, $roleTx) = roleColors($currentRole);

                                // Načti posledních 5 akcí s poznámkou
                                $acts = [];
                                if ($st = $conn->prepare("SELECT section, notes FROM actions_alba_rosa_parlament WHERE idusers_parlament = ? ORDER BY idactions_parlament DESC LIMIT 5")) {
                                    $st->bind_param("i", $uid);
                                    $st->execute();
                                    $rs = $st->get_result();
                                    while ($row = $rs->fetch_assoc()) {
                                        $acts[] = $row;
                                    }
                                    $st->close();
                                }
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($Name) ?></strong></td>
                                    <td><strong><?= htmlspecialchars($lastName) ?></strong></td>
                                    <td><?= htmlspecialchars($email) ?></td>
                                    <td>
                                        <span class="badge role-badge"
                                            style="background: <?= htmlspecialchars($roleBg) ?>; border-color: <?= htmlspecialchars($roleBd) ?>; color: <?= htmlspecialchars($roleTx) ?>;">
                                            <?= $currentRole !== '' ? htmlspecialchars($currentRole) : '— bez role —' ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if (empty($acts)): ?>
                                            <span class="email"><em>Žádné záznamy</em></span>
                                        <?php else: ?>
                                            <ul class="acts">
                                                <?php foreach ($acts as $a):
                                                    $sec = $a['section'] ?? '';
                                                    $cls = '_ucast';
                                                    if ($sec === 'Organizátor akce')
                                                        $cls = '_org';
                                                    elseif ($sec === 'Focení akce')
                                                        $cls = '_foto';
                                                    elseif ($sec === 'Výbor')
                                                        $cls = '_vybor';
                                                    ?>
                                                    <li class="act <?= $cls ?>">
                                                        <div class="row">
                                                            <span class="badge <?= $cls ?>"><?= htmlspecialchars($sec) ?>:</span>
                                                            <span class="note"><?= nl2br(htmlspecialchars($a['notes'] ?? '')) ?></span>
                                                        </div>
                                                    </li>

                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <button class="btn" onclick="openDetail(<?= $uid ?>)"><i class="fa fa-user"></i>
                                            Detail</button>
                                    </td>
                                </tr>

                                <!-- Template obsahu modalu pro tohoto uživatele -->
                                <template id="tpl-<?= $uid ?>">
                                    <div class="modal-head">
                                        <div class="modal-title"><i class="fa fa-user"></i> <?= htmlspecialchars($fullName) ?>
                                            <span class="email">• <?= htmlspecialchars($email) ?></span>
                                        </div>
                                        <div><button class="btn ghost" onclick="closeDetail()"><i class="fa fa-times"></i>
                                                Zavřít</button></div>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        // Souhrny pro modal (počty)
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
                                        ?>
                                        <div class="kpis">
                                            <span class="chip"><i class="fa fa-users"></i> Účast:
                                                <?= (int) ($counts['Účast na akci'] ?? 0) ?></span>
                                            <span class="chip"><i class="fa fa-cogs"></i> Organizátor:
                                                <?= (int) ($counts['Organizátor akce'] ?? 0) ?></span>
                                            <span class="chip"><i class="fa fa-camera"></i> Focení:
                                                <?= (int) ($counts['Focení akce'] ?? 0) ?></span>
                                            <span class="chip"><i class="fa fa-university"></i> Výbor:
                                                <?= (int) ($counts['Výbor'] ?? 0) ?></span>
                                        </div>

                                        <div class="modal-grid">
                                            <div class="card">
                                                <h4><i class="fa fa-id-badge"></i> Role</h4>
                                                <form method="POST" class="form-row">
                                                    <input type="hidden" name="action" value="set_role">
                                                    <input type="hidden" name="idusers_parlament" value="<?= $uid ?>">
                                                    <input class="select" name="role" list="roles-list-<?= $uid ?>"
                                                        value="<?= htmlspecialchars($currentRole) ?>"
                                                        placeholder="Napište roli nebo vyberte…"
                                                        aria-label="Zvolte nebo napište roli" />
                                                    <datalist id="roles-list-<?= $uid ?>">
                                                        <option value="Člen">
                                                        <option value="Vedoucí">
                                                        <option value="Místopředseda">
                                                        <option value="Organizátor">
                                                        <option value="Fotograf">
                                                        <option value="Host">
                                                    </datalist>

                                                    <button class="btn" type="submit"><i class="fa fa-save"></i> Uložit
                                                        roli</button>
                                                </form>
                                            </div>

                                            <div class="card">
                                                <h4><i class="fa fa-plus-circle"></i> Přidat záznam</h4>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="add">
                                                    <input type="hidden" name="idusers_parlament" value="<?= $uid ?>">
                                                    <div class="form-row">
                                                        <select class="select" name="section" required>
                                                            <option value="" disabled selected>-- Vyberte sekci --</option>
                                                            <option value="Účast na akci">Účast na akci</option>
                                                            <option value="Organizátor akce">Organizátor akce</option>
                                                            <option value="Focení akce">Focení akce</option>
                                                            <option value="Výbor">Výbor</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-row">
                                                        <textarea name="notes" placeholder="Poznámka..." required></textarea>
                                                    </div>
                                                    <button class="btn" type="submit"><i class="fa fa-plus"></i> Přidat</button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <h4><i class="fa fa-list-ul"></i> Záznamy</h4>
                                            <div class="notes">
                                                <?php
                                                $q = $conn->prepare("SELECT idactions_parlament, section, notes FROM actions_alba_rosa_parlament WHERE idusers_parlament = ? ORDER BY idactions_parlament DESC");
                                                $q->bind_param("i", $uid);
                                                $q->execute();
                                                $qr = $q->get_result();
                                                if ($qr->num_rows === 0) {
                                                    echo '<div class="email"><em>Žádné záznamy</em></div>';
                                                } else {
                                                    while ($row = $qr->fetch_assoc()) {
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
                                  <input type="hidden" name="idactions_parlament" value="' . (int) $row['idactions_parlament'] . '">
                                  <button class="btn danger" type="submit" title="Odstranit"><i class="fa fa-trash"></i></button>
                                </form>
                              </div>';
                                                    }
                                                }
                                                $q->close();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="error-message"><i class="fa fa-times" style="margin-right:5px;"></i> Chybí oprávnění.</div>
            <?php } ?>

            <?php
            $query = "SELECT text FROM other_alba_rosa_parlament WHERE idother_parlament = 1";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo $row['text'] ?? '';
            } else {
                echo '<div class="error-message">Chyba při získávání dat z databáze: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="personModal" onclick="if(event.target===this) closeDetail();">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="personModalTitle">
            <div id="modalContentSlot"></div>
        </div>
    </div>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script src="../assets/js/script.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', () => {
            const table = document.querySelector('.users');
            const headers = table.querySelectorAll('th.sortable');

            headers.forEach((th, idx) => {
                th.addEventListener('click', () => {
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const current = th.classList.contains('asc') ? 'asc' : th.classList.contains('desc') ? 'desc' : null;

                    // reset ikonek
                    headers.forEach(h => h.classList.remove('asc', 'desc'));

                    // určíme směr
                    const newDir = current === 'asc' ? 'desc' : 'asc';
                    th.classList.add(newDir);

                    rows.sort((a, b) => {
                        const A = a.cells[idx].innerText.trim().toLowerCase();
                        const B = b.cells[idx].innerText.trim().toLowerCase();

                        if (!isNaN(A) && !isNaN(B)) {
                            return newDir === 'asc' ? A - B : B - A;
                        }
                        return newDir === 'asc' ? A.localeCompare(B, 'cs') : B.localeCompare(A, 'cs');
                    });

                    // přidání zpět
                    rows.forEach(r => tbody.appendChild(r));
                });
            });
        });
    </script>

</body>

</html>