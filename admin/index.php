<?php
include '../assets/php/config.php';
session_start();
ob_start();

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
    $latestClassLabel = null;
    if (!empty($idusers_parlament)) {
        if (
            $stc = $conn->prepare("
            SELECT class_year, class_name
            FROM classes_alba_rosa_parlament
            WHERE idusers_parlament = ?
            ORDER BY class_year DESC, idclass_parlament DESC
            LIMIT 1
        ")
        ) {
            $stc->bind_param("i", $idusers_parlament);
            $stc->execute();
            $rc = $stc->get_result();
            if ($c = $rc->fetch_assoc()) {
                $y = (int) $c['class_year'];
                $className = trim((string) $c['class_name']);
                $latestClassLabel = ($y > 0 ? ($y . '/' . ($y + 1)) : '') . ($className !== '' ? ' – ' . $className : '');
            }
            $stc->close();
        }
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

// ---------- Set role (NOVĚ: zapisuje do roles_alba_rosa_parlament) ----------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'set_role') {
    $id = (int) ($_POST['idusers_parlament'] ?? 0);

    $role = trim($_POST['role'] ?? '');
    $role = preg_replace('/\s+/u', ' ', $role);
    if (function_exists('mb_strlen') && mb_strlen($role) > 50) {
        $role = mb_substr($role, 0, 50);
    } elseif (strlen($role) > 50) {
        $role = substr($role, 0, 50);
    }

    if ($id <= 0 || $role === '') {
        redirectWithMessage("Zadejte platnou roli i uživatele.", "info-message");
    }

    $stmt = $conn->prepare("INSERT INTO roles_alba_rosa_parlament (idusers_parlament, role, assigned_at) VALUES (?, ?, NOW())");
    if ($stmt) {
        $stmt->bind_param("is", $id, $role);
        if ($stmt->execute()) {
            redirectWithMessage("Role byla přiřazena (uloženo do historie).", "success-message");
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
    $school_year_raw = trim($_POST['school_year'] ?? '');
    $class_name = trim($_POST['class_name'] ?? '');

    if (!preg_match('/^\\d{4}\\s*\\/\\s*\\d{4}$/', $school_year_raw)) {
        redirectWithMessage("Zadejte školní rok ve formátu RRRR/RRRR (např. 2024/2025).", "info-message");
    }

    list($startY, $endY) = preg_split('/\\s*\\/\\s*/', $school_year_raw);
    $startY = (int) $startY;
    $endY = (int) $endY;

    if ($endY !== $startY + 1 || $startY < 2000 || $startY > 2100) {
        redirectWithMessage("Neplatný školní rok. Zadejte např. 2024/2025).", "info-message");
    }

    if ($id > 0 && $class_name !== '') {
        if (function_exists('mb_strlen') && mb_strlen($class_name) > 50) {
            $class_name = mb_substr($class_name, 0, 50);
        } elseif (strlen($class_name) > 50) {
            $class_name = substr($class_name, 0, 50);
        }

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

// ---------- Load distinct roles for quick filters ----------
$rolesDistinct = [];
if (
    $res = $conn->query("SELECT DISTINCT role 
                         FROM roles_alba_rosa_parlament 
                         WHERE role IS NOT NULL AND role <> '' 
                         ORDER BY role")
) {
    while ($row = $res->fetch_assoc()) {
        $rolesDistinct[] = $row['role'];
    }
    $res->close();
}

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

            /* === sjednocení „bublin“ + nová výška stacků === */
            --chip-px: 8px;
            --chip-py: 4px;
            --chip-fs: 12px;
            --chip-lh: 1.25;
            --chip-radius: 999px;
            --chip-gap: 8px;

            --row-gap: 6px;
            --avatar-size: 32px;

            --stack-max: 130px;
        }

        body {
            background: #f6f8fb;
            color: var(--text);
        }

        .wrap {
            margin: 0 auto;
            padding: 14px;
        }

        .table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 12px 0 18px;
        }

        .table-heading h2 {
            margin: 0;
            font-family: "Roboto Slab", serif
        }

        .table-heading .blue {
            color: var(--brand);
        }

        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center
        }

        .input {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 10px;
            background: #fff
        }

        .input:focus {
            outline: none;
            border-color: #c8d7ee;
            box-shadow: 0 0 0 3px rgba(84, 129, 170, .15)
        }

        .table-wrap {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: auto;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
            max-height: 72vh;
            position: relative
        }

        table.users {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1100px
        }

        .users th,
        .users td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top
        }

        .users thead th {
            position: sticky;
            top: 0;
            background: #5481aa;
            color: #ffffffff;
            z-index: 2;
            border-bottom: 1px solid #dbe3ef
        }

        .users thead tr:first-child th {
            box-shadow: 0 1px 0 rgba(0, 0, 0, .03)
        }

        .users tbody tr:hover {
            background: #f9fbff
        }

        .users th:first-child,
        .users td:first-child {
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 1
        }

        .users th:first-child {
            background: #5481aa;
            z-index: 3
        }

        /* Výchozí barva řádku */
        .users tbody tr { --row-bg: #fff; }
        /* Barva při hoveru */
        .users tbody tr:hover { --row-bg: #f9fbff; }
        /* Aplikuje se na všechny buňky v řádku */
        .users tbody tr > * { background: var(--row-bg); }

        /* Lepivý první sloupec – bez pevné barvy, aby převzalo var(--row-bg) */
        .users th:first-child,
        .users td:first-child {
          position: sticky;
          left: 0;
          z-index: 1;
        }

        /* Hlavička musí zůstat modrá */
        .users thead th { background: #5481aa; color: #fff; }
        .users thead th:first-child { z-index: 3; }

        .header-sub {
            font-size: 12px;
            color: #ffffffff;
            font-weight: 400
        }

        /* === sjednocené chipy === */
        .badge,
        .role-chip,
        .class-chip,
        .acts .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: var(--chip-py) var(--chip-px);
            border-radius: var(--chip-radius);
            font-size: var(--chip-fs);
            line-height: var(--chip-lh);
            white-space: nowrap;
            background: var(--chip);
            border: 1px solid var(--border);
            vertical-align: middle;
        }

        .badge.small {
            padding: calc(var(--chip-py) - 1px) calc(var(--chip-px) - 2px);
            font-size: var(--chip-fs);
        }

        .badge.role-badge {
            letter-spacing: .2px;
            font-weight: 600;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .avatar {
            width: var(--avatar-size);
            height: var(--avatar-size);
            border-radius: 50%;
            background: #e5eef9;
            color: #355170;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700
        }

        .user-meta {
            display: flex;
            flex-direction: column
        }

        .user-meta .name {
            font-weight: 700
        }

        .user-meta .email {
            font-size: 12px;
            color: #64748b;
            max-width: 340px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Role řádek – stejné chování desktop/mobil */
        .roles-line {
            display: flex;
            gap: var(--chip-gap);
            flex-wrap: wrap;
            align-items: center;
            margin-top: 6px;
        }

        /* === svislé seznamy (stack) pro Třídy a Role, jako u Akcí === */
        .stack-list,
        .acts {
            max-height: var(--stack-max);
            overflow: auto;
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: var(--row-gap);
        }

        .class-chip .sep {
            padding: 0 4px;
            opacity: .6
        }

        .actions {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .btn {
            border: none;
            background: var(--brand);
            color: #fff;
            padding: 8px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: background .2s
        }

        .btn:hover {
            background: var(--brand-2)
        }

        .btn.icon {
            padding: 6px 9px;
            border-radius: 8px
        }

        .btn.ghost {
            background: #fff;
            color: #334155;
            border: 1px solid var(--border)
        }

        .btn.copy {
            background: #e9eef6;
            color: #334155
        }

        /* tlačítko Detail jako chip (stejné rozměry) */
        .btn.detail-chip {
            background: #fff;
            color: #334155;
            border: 1px solid var(--border);
            font-size: var(--chip-fs);
            line-height: var(--chip-lh);
            padding: var(--chip-py) var(--chip-px);
            border-radius: var(--chip-radius);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .success-message,
        .error-message,
        .info-message {
            margin: 12px 0;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid;
            cursor: pointer
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

        .th-flex {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .sortable {
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            position: relative
        }

        .sortable:after {
            content: '\f0dc';
            font-family: FontAwesome;
            position: absolute;
            right: 8px;
            top: 10px;
            opacity: .4
        }

        thead th.sortable.asc:after {
            content: '\f0de';
            opacity: .9
        }

        thead th.sortable.desc:after {
            content: '\f0dd';
            opacity: .9
        }

        .quick-filters {
            display: flex;
            gap: 6px;
            flex-wrap: wrap
        }

        .quick-filters .qf {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid #d9e1ee;
            background: #fff;
            cursor: pointer
        }

        .qf.active {
            background: #e7f0ff;
            border-color: #b5cff7
        }

        .copy-tip {
            font-size: 11px;
            color: #6b7280;
            margin-left: 6px
        }

        .muted {
            color: #6b7280
        }

        /* Akce barevné badge – drží jednotnou velikost */
        .acts ._ucast .badge {
            background: #ecfdf5;
            border-color: #bbf7d0;
            color: #065f46;
        }

        .acts ._org .badge {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e3a8a;
        }

        .acts ._foto .badge {
            background: #f5f3ff;
            border-color: #ddd6fe;
            color: #5b21b6;
        }

        .acts ._vybor .badge {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #9a3412;
        }

        /* lepší zarovnání sloupce Detail */
        td.actions {
            width: 1%;
            white-space: nowrap;
            vertical-align: top;
        }

        /* === Responsivní zlepšení (mobily & tablety) === */
        @media (max-width: 1024px) {
          .wrap { padding: 10px; }
          .table-heading { flex-direction: column; align-items: stretch; gap: 8px; }
          .toolbar { gap: 8px; }
          .toolbar .input { width: 100%; }            /* search na plnou šířku */
          .quick-filters { 
            overflow-x: auto; 
            padding-bottom: 4px; 
            -webkit-overflow-scrolling: touch;
          }
          .quick-filters .qf { flex: 0 0 auto; }
        }

        @media (max-width: 900px) {
          /* Štítky tříd/rolí zarovnáme vedle sebe a necháme zalamovat */
          .stack-list { 
            flex-direction: row; 
            flex-wrap: wrap; 
            gap: 8px;
          }
          .stack-list li { display: inline-flex; }
          /* z tabulky uděláme karty */
          .table-wrap { 
            max-height: none; 
            overflow: visible; 
            border: none; 
            background: transparent; 
            box-shadow: none; 
          }
          table.users { min-width: 0; border-collapse: collapse; }
          .users thead { display: none; }             /* hlavičku skryjeme – název sloupce doplní ::before v každé buňce */
          .users tbody tr { 
            display: block; 
            margin: 10px 0; 
            padding: 12px; 
            background: var(--card); 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            box-shadow: 0 6px 18px rgba(15,23,42,.06); 
          }
          .users tbody tr > td { 
            display: block; 
            padding: 8px 0; 
            border: 0; 
            background: transparent; 
          }
          .users tbody tr > td:first-child { padding-top: 0; }
          .users tbody tr > td + td { 
            border-top: 1px dashed var(--border); 
            margin-top: 10px; 
            padding-top: 12px; 
          }
          .users tbody tr > td::before {
            content: attr(data-label);
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
          }

          /* zrušit lepivý 1. sloupec na mobilech */
          .users th:first-child,
          .users td:first-child { position: static; }

          /* omezení výšky stacků už není potřeba na kartách */
          .stack-list, .acts { max-height: none; }

          .btn.detail-chip { padding: 6px 10px; border-radius: 8px; }
          .user-meta .email { max-width: 100%; }
        }

        @media (max-width: 480px) {
          :root { --avatar-size: 36px; }
          .user-meta .name { font-size: 15px; }
          .badge, .role-chip, .class-chip, .acts .badge { font-size: 12px; }
          .btn { width: auto; }   /* zachovat stejné tlačítko jako na desktopu */
        }
    </style>

    <script>
        function removeQueryString() {
            const url = new URL(window.location);
            url.searchParams.delete('message'); url.searchParams.delete('message_type');
            window.history.replaceState({}, '', url);
            document.querySelectorAll('.success-message,.error-message,.info-message').forEach(b => b.style.display = 'none');
        }

        function initialAvatar(name) {
            if (!name) return '?';
            const parts = name.trim().split(/\s+/).slice(0, 2);
            return parts.map(s => s.charAt(0).toUpperCase()).join('');
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Fill avatar initials
            document.querySelectorAll('[data-initials]').forEach(el => {
                el.textContent = initialAvatar(el.getAttribute('data-initials'));
            });

            // Search
            const search = document.getElementById('search');
            const tbody = document.querySelector('table.users tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            function applyFilter() {
                const q = (search.value || '').toLowerCase();
                rows.forEach(tr => {
                    const hay = (tr.getAttribute('data-haystack') || '').toLowerCase();
                    tr.style.display = hay.includes(q) ? '' : 'none';
                });
            }
            if (search) { search.addEventListener('input', applyFilter); }

            // Sorting (incl. custom for classes)
            const table = document.querySelector('.users');
            if (table) {
                const headers = table.querySelectorAll('th.sortable');
                const classTh = table.querySelector('th[data-sort-key="class"]');
                let classSortMode = 'year';
                let nextDirOverride = null;

                headers.forEach((th, idx) => {
                    th.addEventListener('click', () => {
                        const tbody = table.querySelector('tbody');
                        const rs = Array.from(tbody.querySelectorAll('tr'));
                        const current = th.classList.contains('asc') ? 'asc' : th.classList.contains('desc') ? 'desc' : null;
                        headers.forEach(h => h.classList.remove('asc', 'desc'));
                        let newDir = current === 'asc' ? 'desc' : 'asc';
                        if (nextDirOverride) { newDir = nextDirOverride; nextDirOverride = null; }
                        th.classList.add(newDir);

                        const sortKey = th.dataset.sortKey || '';
                        rs.sort((a, b) => {
                            const cellA = a.cells[idx], cellB = b.cells[idx];
                            if (sortKey === 'class') {
                                if (classSortMode === 'year') {
                                    const Ay = parseInt(cellA?.getAttribute('data-sort-year') || '0', 10);
                                    const By = parseInt(cellB?.getAttribute('data-sort-year') || '0', 10);
                                    return newDir === 'asc' ? Ay - By : By - Ay;
                                } else {
                                    const An = (cellA?.getAttribute('data-sort-name') || '').toLowerCase();
                                    const Bn = (cellB?.getAttribute('data-sort-name') || '').toLowerCase();
                                    return newDir === 'asc' ? An.localeCompare(Bn, 'cs') : Bn.localeCompare(An, 'cs');
                                }
                            }
                            const A = (cellA?.innerText || '').trim().toLowerCase();
                            const B = (cellB?.innerText || '').trim().toLowerCase();
                            const numA = parseFloat(A.replace(',', '.'));
                            const numB = parseFloat(B.replace(',', '.'));
                            const bothNumbers = !isNaN(numA) && !isNaN(numB);
                            if (bothNumbers) { return newDir === 'asc' ? numA - numB : numB - numA; }
                            return newDir === 'asc' ? A.localeCompare(B, 'cs') : B.localeCompare(A, 'cs');
                        });
                        rs.forEach(r => tbody.appendChild(r));
                    });
                });

                if (classTh) {
                    classTh.querySelectorAll('.sort-toggle').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            classSortMode = btn.dataset.mode;
                            classTh.querySelectorAll('.sort-toggle').forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                            const current = classTh.classList.contains('asc') ? 'asc' : classTh.classList.contains('desc') ? 'desc' : 'asc';
                            nextDirOverride = current;
                            classTh.click();
                        });
                    });
                }
            }

            // Copy email
            document.querySelectorAll('[data-copy]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const text = btn.getAttribute('data-copy');
                    navigator.clipboard.writeText(text).then(() => {
                        btn.textContent = 'Zkopírováno';
                        setTimeout(() => { btn.textContent = 'Kopírovat e-mail'; }, 1200);
                    });
                });
            });

            // Quick filters by role (click chip)
            document.querySelectorAll('.qf').forEach(chip => {
                chip.addEventListener('click', () => {
                    document.querySelectorAll('.qf').forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    const val = chip.getAttribute('data-val') || '';
                    const search = document.getElementById('search');
                    search.value = val;
                    search.dispatchEvent(new Event('input'));
                });
            });
        });
    </script>
</head>

<body>
    <div id="calendar">
        <div class="overlay" id="overlay" onclick="closeAllMenus()"></div>

        <nav>
            <div class="user-icon" onclick="toggleUserMenu(event)">
                <?php if (!empty($username_parlament)) { ?>
                    <i class="fa fa-user" style="color:#5481aa;"></i>
                <?php } else { ?>
                    <i class="fa fa-user" style="color:#3C3C3B;"></i>
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

            <div class="hamburger" onclick="toggleMobileMenu(event)">
                <i class="fa fa-bars"></i>
            </div>

            <div class="user-dropdown" id="userDropdown">
                <?php if (!empty($username_parlament)) { ?>
                    <p style="margin-top:0;">Přihlášen/a jako:
                        <b><?php echo htmlspecialchars($username_parlament); ?></b><br>
                        <?php if (!empty($latestClassLabel)) { ?>
                            <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                            <span>Nejnovější třída: <b><?php echo htmlspecialchars($latestClassLabel); ?></b></span>
                        <?php } ?>
                    </p>
                    <a href="../logout.php">Odhlásit se</a>
                <?php } else { ?>
                    <a class="popup-trigger" data-link="../login.php">Přihlásit se</a>
                <?php } ?>
            </div>

            <div class="mobile-menu" id="mobileMenu">
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
                    <div class="toolbar">
                        <input id="search" class="input" type="search" placeholder="🔎 Hledat jméno, e-mail, roli, třídu…">
                        <span class="quick-filters">
                            <button class="qf active" data-val="">Vše</button>
                            <?php foreach ($rolesDistinct as $r): ?>
                                <button class="qf" data-val="<?php echo htmlspecialchars($r); ?>">
                                    <?php echo htmlspecialchars($r); ?>
                                </button>
                            <?php endforeach; ?>
                        </span>

                    </div>
                </div>

                <div class="table-wrap">
                    <table class="users">
                        <thead>
                            <tr>
                                <th class="sortable" style="width: 1%;">Osoba<br><span class="header-sub">jméno, e-mail,
                                        aktuální role</span>
                                </th>
                                <th class="sortable" data-sort-key="class">
                                    <div class="th-flex">
                                        <span>Třídy</span>
                                        <span class="sort-switch" aria-label="Režim řazení tříd"
                                            style="display:inline-flex;gap:4px;background:#eef4ff;border-radius:999px;padding:2px 2px 2px 6px">
                                            <button type="button" class="sort-toggle active" data-mode="year"
                                                title="Řadit podle roku"
                                                style="border:0;background:transparent;font-size:12px;cursor:pointer">rok</button>
                                            <button type="button" class="sort-toggle" data-mode="name"
                                                title="Řadit podle třídy"
                                                style="border:0;background:transparent;font-size:12px;cursor:pointer">třída</button>
                                        </span>
                                    </div>
                                    <div class="header-sub">poslední záznamy</div>
                                </th>
                                <th class="sortable">Role & historie<br><span class="header-sub">posledních 5 (RRRR –
                                        role)</span></th>
                                <th>Akce (posledních 5)</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <?php
                                $uid = (int) $user['idusers_parlament'];
                                $Name = ($user['name'] ?? '');
                                $lastName = ($user['last_name'] ?? '');
                                $email = $user['email'] ?? '';
                                $fullName = trim($Name . ' ' . $lastName);

                                // Akce (posledních 5)
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

                                // Třídy
                                $classes = [];
                                $latestYear = 0;
                                $nameParts = [];
                                if ($stc = $conn->prepare("SELECT idclass_parlament, class_year, class_name FROM classes_alba_rosa_parlament WHERE idusers_parlament = ? ORDER BY class_year DESC, idclass_parlament DESC")) {
                                    $stc->bind_param("i", $uid);
                                    $stc->execute();
                                    $rc = $stc->get_result();
                                    while ($c = $rc->fetch_assoc()) {
                                        $classes[] = $c;
                                        $y = (int) $c['class_year'];
                                        if ($y > $latestYear)
                                            $latestYear = $y;
                                        $nameParts[] = function_exists('mb_strtolower') ? mb_strtolower(trim($c['class_name']), 'UTF-8') : strtolower(trim($c['class_name']));
                                    }
                                    $stc->close();
                                }
                                $sortName = !empty($nameParts) ? implode(';', $nameParts) : '';

                                // Role (posledních 5)
                                $roleRows = [];
                                if ($str = $conn->prepare("SELECT role, assigned_at FROM roles_alba_rosa_parlament WHERE idusers_parlament = ? ORDER BY assigned_at DESC, idrole DESC LIMIT 5")) {
                                    $str->bind_param("i", $uid);
                                    $str->execute();
                                    $rr = $str->get_result();
                                    while ($r = $rr->fetch_assoc()) {
                                        $roleRows[] = $r;
                                    }
                                    $str->close();
                                }

                                $latestRoleText = '';
                                if (!empty($roleRows)) {
                                    $latestRoleText = (string) $roleRows[0]['role'];
                                }
                                list($roleBg, $roleBd, $roleTx) = roleColors($latestRoleText);

                                // Haystack for search
                                $hay = strtolower($fullName . ' ' . $email . ' ' . implode(' ', array_map(function ($c) {
                                    return $c['class_name'];
                                }, $classes)) . ' ' . implode(' ', array_map(function ($r) {
                                    return $r['role'];
                                }, $roleRows)));
                                ?>
                                <tr data-haystack="<?php echo htmlspecialchars($hay); ?>">
                                    <td data-label="Osoba">
                                        <div class="user-cell">
                                            <span class="avatar" aria-hidden="true"
                                                data-initials="<?php echo htmlspecialchars($fullName ?: $email); ?>"></span>
                                            <div class="user-meta">
                                                <span
                                                    class="name"><?php echo htmlspecialchars($fullName ?: 'Neznámý'); ?></span>
                                                <span class="email"><?php echo htmlspecialchars($email ?: '—'); ?></span>
                                                <div class="roles-line">
                                                    <span class="badge role-badge"
                                                        style="background: <?php echo htmlspecialchars($roleBg) ?>; border-color: <?php echo htmlspecialchars($roleBd) ?>; color: <?php echo htmlspecialchars($roleTx) ?>;">
                                                        <?php echo $latestRoleText !== '' ? htmlspecialchars($latestRoleText) : '— bez role —'; ?>
                                                    </span>
                                                    <?php if ($email): ?>
                                                        <button class="btn detail-chip popup-trigger"
                                                            data-link="detail_user_parlament.php?idusers_parlament=<?php echo $uid; ?>">
                                                            <i class="fa fa-user"></i> Detail
                                                        </button>

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td data-label="Třídy"
                                        data-sort-year="<?php echo $latestYear > 0 ? $latestYear : 0; ?>"
                                        data-sort-name="<?php echo htmlspecialchars($sortName); ?>">
                                        <?php if (empty($classes)): ?>
                                            <span class="muted">—</span>
                                        <?php else: ?>
                                            <ul class="stack-list">
                                                <?php foreach ($classes as $c):
                                                    $y = (int) $c['class_year']; ?>
                                                    <li>
                                                        <span class="class-chip"
                                                            title="<?php echo htmlspecialchars(($y . '/' . ($y + 1)) . ', ' . $c['class_name']); ?>">
                                                            <span class="year"><?php echo $y; ?>/<?php echo $y + 1; ?></span>
                                                            <span class="sep">–</span>
                                                            <span class="cls"><?php echo htmlspecialchars($c['class_name']); ?></span>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>

                                    <td data-label="Role &amp; historie">
                                        <?php if (empty($roleRows)): ?>
                                            <span class="muted">— bez historie —</span>
                                        <?php else: ?>
                                            <ul class="stack-list">
                                                <?php foreach ($roleRows as $r):
                                                    $dateFull = '';
                                                    if (!empty($r['assigned_at'])) {
                                                        $ts = strtotime($r['assigned_at']);
                                                        $dateFull = $ts ? date('Y-m-d', $ts) : '';
                                                    } ?>
                                                    <li>
                                                        <span class="role-chip">
                                                            <?php echo $dateFull !== '' ? htmlspecialchars($dateFull) : '—'; ?>
                                                            – <?php echo htmlspecialchars($r['role']); ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>

                                    <td data-label="Akce">
                                        <?php if (empty($acts)): ?>
                                            <span class="muted"><em>Žádné záznamy</em></span>
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
                                                    <li class="act <?php echo $cls; ?>">
                                                        <span class="badge <?php echo $cls; ?>">
                                                            <?php echo htmlspecialchars($sec); ?>:
                                                        </span>
                                                        <span class="note">
                                                            <?php echo nl2br(htmlspecialchars($a['notes'] ?? '')); ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
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

    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-content">
            <button class="popup-close" id="popupClose">&times;</button>
            <iframe class="popup-iframe" id="popupIframe" src=""></iframe>
        </div>
    </div>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>
