<?php
include '../assets/php/config.php';
session_start();
ob_start();

if (!function_exists('roleColors')) {
    function roleColors(string $role): array
    {
        if ($role === '') {
            // neutrální "bez role"
            return ['#f3f4f6', '#e5e7eb', '#111827'];
        }

        // stabilní hash z textu (diakritika nevadí)
        $source = function_exists('mb_strtolower') ? mb_strtolower($role, 'UTF-8') : strtolower($role);
        $hash = crc32($source);

        // 1) rozprostření přes "zlatý úhel" + 2) kvantizace na kroky (větší rozestup odstínů)
        $golden = 0.61803398875;                                  // zlatý poměr (konjugát)
        $base = fmod(($hash * $golden) * 360.0, 360.0);           // rozprostřený hue
        $STEP_DEG = 20;                                           // krok kvantizace (čím větší, tím víc rozdílné barvy)
        $bucket = (int) round($base / $STEP_DEG);
        $h = ($bucket * $STEP_DEG) % 360;

        // Jemné obměny saturace/světlosti, ale pořád pastel
        $satChoices = [70, 78, 85, 90];
        $lightBg = [92, 94, 96];

        $s = $satChoices[$hash & 3];
        $lBg = $lightBg[($hash >> 2) % 3];

        // okraj o něco tmavší a s mírně vyšší saturací
        $lBd = max(65, $lBg - 18);
        $sBd = min(95, $s + 6);

        // text necháme tmavý – na pastelu čitelný
        $tx = '#0f172a';

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

// ---------- Add class ----------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_class') {
    $id = (int) ($_POST['idusers_parlament'] ?? 0);
    $school_year_raw = trim($_POST['school_year'] ?? '');  // očekáváme "YYYY/YYYY"
    $class_name = trim($_POST['class_name'] ?? '');

    // validace formátu YYYY/YYYY
    if (!preg_match('/^\\d{4}\\s*\\/\\s*\\d{4}$/', $school_year_raw)) {
        redirectWithMessage("Zadejte školní rok ve formátu RRRR/RRRR (např. 2024/2025).", "info-message");
    }

    // rozpad + sanity check (druhý rok = první + 1)
    list($startY, $endY) = preg_split('/\\s*\\/\\s*/', $school_year_raw);
    $startY = (int) $startY;
    $endY = (int) $endY;

    if ($endY !== $startY + 1 || $startY < 2000 || $startY > 2100) {
        redirectWithMessage("Neplatný školní rok. Zadejte např. 2024/2025.", "info-message");
    }

    if ($id > 0 && $class_name !== '') {
        if (mb_strlen($class_name) > 50)
            $class_name = mb_substr($class_name, 0, 50);

        // do DB ukládáme pouze počáteční rok (startY) do sloupce class_year (INT)
        $stmt = $conn->prepare("INSERT INTO classes_alba_rosa_parlament (idusers_parlament, class_year, class_name) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iis", $id, $startY, $class_name);
            if ($stmt->execute()) {
                redirectWithMessage("Třída přidána.", "success-message");
            } else {
                redirectWithMessage("Nepodařilo se přidat třídu.", "error-message");
            }
        } else {
            redirectWithMessage("Chyba v dotazu.", "error-message");
        }
    } else {
        redirectWithMessage("Vyplňte název třídy.", "info-message");
    }
}

// ---------- Delete class ----------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete_class') {
    $delete_id = (int) ($_POST['idclass_parlament'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM classes_alba_rosa_parlament WHERE idclass_parlament = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            redirectWithMessage("Třída byla odstraněna.", "success-message");
        } else {
            redirectWithMessage("Chyba při mazání třídy.", "error-message");
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

        .class-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .class-chip {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 12.5px;
            white-space: nowrap;
        }

        .class-chip .year {
            font-weight: 700;
        }

        .class-chip .sep {
            padding: 0 4px;
            opacity: .6;
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
            min-width: 1050px;
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
            z-index: 1;
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

        /* Akce v tabulce (posledních 5) */
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


        /* ---- /EXISTUJÍCÍ POPUP STYL ---- */

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
                                <th class="sortable">Třídy</th>
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
                                $classes = [];
                                $latestYear = 0;
                                if ($stc = $conn->prepare("SELECT idclass_parlament, class_year, class_name FROM classes_alba_rosa_parlament WHERE idusers_parlament = ? ORDER BY class_year DESC, idclass_parlament DESC")) {
                                    $stc->bind_param("i", $uid);
                                    $stc->execute();
                                    $rc = $stc->get_result();
                                    while ($c = $rc->fetch_assoc()) {
                                        $classes[] = $c;
                                        $y = (int) $c['class_year'];
                                        if ($y > $latestYear)
                                            $latestYear = $y;
                                    }
                                    $stc->close();
                                }
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($Name) ?></strong></td>
                                    <td><strong><?= htmlspecialchars($lastName) ?></strong></td>
                                    <td><?= htmlspecialchars($email) ?></td>
                                    <td data-sort="<?= $latestYear > 0 ? $latestYear : 0 ?>">
                                        <?php if (empty($classes)): ?>
                                            <span class="muted">—</span>
                                        <?php else: ?>
                                            <div class="class-list">
                                                <?php foreach ($classes as $c):
                                                    $y = (int) $c['class_year']; ?>
                                                    <span class="class-chip"
                                                        title="<?= htmlspecialchars(($y . '/' . ($y + 1)) . ', ' . $c['class_name']) ?>">
                                                        <span class="year"><?= $y ?>/<?= $y + 1 ?></span>
                                                        <span class="sep">–</span>
                                                        <span class="cls"><?= htmlspecialchars($c['class_name']) ?></span>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

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
                                        <button class="btn popup-trigger"
                                            data-link="detail_user_parlament.php?idusers_parlament=<?= $uid ?>">
                                            <i class="fa fa-user"></i> Detail
                                        </button>
                                    </td>
                                </tr>
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

    <!-- EXISTUJÍCÍ POPUP OVERLAY (markup pouze jednou na stránce) -->
    <!-- Popup struktura -->
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-content">
            <button class="popup-close" id="popupClose">&times;</button>
            <iframe class="popup-iframe" id="popupIframe" src=""></iframe>
        </div>
    </div>
    <!-- /POPUP OVERLAY -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script src="../assets/js/script.js"></script>

    <script>
        // Řazení podle záhlaví
        document.addEventListener('DOMContentLoaded', () => {
            const table = document.querySelector('.users');
            if (!table) return;
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
                        // na třídách preferujeme data-sort (poslední rok)
                        const cellA = a.cells[idx];
                        const cellB = b.cells[idx];
                        const sortA = cellA?.getAttribute('data-sort');
                        const sortB = cellB?.getAttribute('data-sort');

                        if (sortA !== null || sortB !== null) {
                            const AA = parseInt(sortA || '0', 10);
                            const BB = parseInt(sortB || '0', 10);
                            return newDir === 'asc' ? AA - BB : BB - AA;
                        }

                        const A = (cellA?.innerText || '').trim().toLowerCase();
                        const B = (cellB?.innerText || '').trim().toLowerCase();

                        const numA = parseFloat(A.replace(',', '.'));
                        const numB = parseFloat(B.replace(',', '.'));
                        const bothNumbers = !isNaN(numA) && !isNaN(numB);

                        if (bothNumbers) {
                            return newDir === 'asc' ? numA - numB : numB - numA;
                        }
                        return newDir === 'asc' ? A.localeCompare(B, 'cs') : B.localeCompare(A, 'cs');
                    });

                    // Přidání zpět
                    rows.forEach(r => tbody.appendChild(r));
                });
            });
        });
    </script>

</body>

</html>