<?php
include '../assets/php/config.php';
session_start();

if (!isset($_SESSION['idusers_parlament'])) {
    echo 'Neautorizovaný přístup';
    exit;
}

if (!isset($_GET['idattendances_list_parlament'])) {
    echo 'Chybí ID listiny';
    exit;
}

$idattendances_list_parlament = intval($_GET['idattendances_list_parlament']);
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <link rel="manifest" href="./assets/json/manifest.json">
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- OG Metadata -->
    <meta property="og:title" content="Alba-rosa.cz | Parlament na Purkyňce" />
    <meta property="og:url" content="https://www.alba-rosa.cz/" />
    <meta property="og:image" content="https://www.alba-rosa.cz/parlament/logo.png" />
    <meta property="og:description"
        content="Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a podílejí se na chodu školy. Organizuje akce, řeší problémy a komunikuje s vedením školy." />
    <meta name="theme-color" content="#5481aa" data-react-helmet="true" />

    <!-- Meta description pro SEO -->
    <meta name="description"
        content="Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a podílejí se na chodu školy. Organizuje akce, řeší problémy a komunikuje s vedením školy. Zapojení rozvíjí komunikační a organizační dovednosti a umožňuje ovlivnit dění ve škole." />

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .student-item {
            display: block;
            background: #fff;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .student-info {
            margin-bottom: 5px;
        }

        .student-info span {
            display: block;
        }

        .student-info .name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .student-info .email {
            font-size: 14px;
            color: #777;
        }

        .student-info .time {
            font-size: 18px;
            font-weight: bold;
        }

        .save-button {
            background-color: #5481aa;
            color: white;
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .save-button:hover {
            background-color: #77afe0;
            transform: scale(1.05);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div id="calendar">

        <?php
        if (isset($_GET['message']) && isset($_GET['message_type'])) {
            $message = $_GET['message'];
            $message_type = $_GET['message_type'];

            // Určení třídy a ikony podle typu zprávy
            if ($message_type == 'success-message') {
                $message_class = 'success-message';
                $message_icon = 'fa-check';
            } elseif ($message_type == 'error-message') {
                $message_class = 'error-message';
                $message_icon = 'fa-times';
            } elseif ($message_type == 'info-message') {
                $message_class = 'info-message';
                $message_icon = 'fa-info-circle';
            }

            // Převod HTML zpět, aby seznamy fungovaly správně
            $decoded_message = htmlspecialchars_decode($message);

            // Výstup zprávy s ikonou a třídou
            echo '<div onclick="removeQueryString()" class="' . $message_class . '" style="cursor: pointer;">';
            echo '<i class="fa ' . $message_icon . '" style="margin-right: 5px;"></i> ' . $decoded_message;
            echo '</div>';
        }
        ?>
        <h2>Seznam studentů</h2>
        <form id="attendanceForm">
            <div id="studentContainer">Načítání studentů...</div>
            <input type="hidden" name="idattendances_list_parlament" value="<?= $idattendances_list_parlament ?>">
            <button type="submit" class="save-button">Uložit prezenční listinu</button>
        </form>
    </div>
    <script>
        const container = document.getElementById('studentContainer');
        const idListiny = <?= $idattendances_list_parlament ?>;

        fetch(`../attendances/fetch_students.php?idattendances_list_parlament=${idListiny}&timestamp=${Date.now()}`)
            .then(response => response.json())
            .then(data => {
                if (!Array.isArray(data.students) || data.students.length === 0) {
                    container.innerHTML = '<div style="text-align: center;">Žádní studenti nenalezeni.</div>';
                    return;
                }

                container.innerHTML = '';
                data.students.forEach((student, index) => {
                    const row = document.createElement('div');
                    row.className = 'student-item';

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'present[]';
                    checkbox.value = student.id;
                    checkbox.checked = data.present.includes(student.id);
                    checkbox.style.marginRight = '10px';
                    checkbox.onclick = e => e.stopPropagation();

                    const info = document.createElement('div');
                    info.className = 'student-info';

                    const nameSpan = document.createElement('span');
                    nameSpan.className = 'name';
                    nameSpan.innerHTML = `${index + 1}. <strong>${student.last_name} ${student.name}</strong>`;

                    const emailSpan = document.createElement('span');
                    emailSpan.className = 'email';
                    emailSpan.innerHTML = `<em>(${student.email})</em>`;

                    const timeSpan = document.createElement('span');
                    timeSpan.className = 'time';
                    timeSpan.textContent = student.time;
                    timeSpan.style.color = student.time === 'nepřítomen' ? 'red' : 'green';

                    info.append(nameSpan, emailSpan, timeSpan);

                    row.onclick = () => checkbox.checked = !checkbox.checked;
                    row.append(checkbox, info);
                    container.appendChild(row);
                });
            })
            .catch(err => {
                console.error('Chyba při načítání studentů:', err);
                container.innerHTML = '<div style="text-align: center; color: red;">Nepodařilo se načíst studenty.</div>';
            });

        document.getElementById('attendanceForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const checkboxes = document.querySelectorAll('input[name="present[]"]');
            const attendanceData = [];

            checkboxes.forEach(checkbox => {
                attendanceData.push({
                    idusers_parlament: checkbox.value,
                    present: checkbox.checked
                });
            });

            fetch('save_attendance_list.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    idattendances_list_parlament: idListiny,
                    attendanceData: attendanceData
                })
            })
                .then(res => res.json())
                .then(result => {
                    const message = encodeURIComponent(result.message);
                    const type = result.success ? 'success-message' : 'error-message';
                    window.location.href = `./show_students.php?message=${message}&message_type=${type}&idattendances_list_parlament=${idListiny}`;
                })
                .catch(err => {
                    console.error('Chyba při ukládání:', err);
                    const message = encodeURIComponent('Nepodařilo se uložit prezenční listinu.');
                    window.location.href = `./show_students.php?message=${message}&message_type=error-message&idattendances_list_parlament=${idListiny}`;
                });
        });
    </script>
</body>

</html>