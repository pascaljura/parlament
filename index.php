<?php
include './assets/php/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/favicon.ico" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <?php
    $headerText = '&#x1F499;・Zápisy・2023/2024';
    ?>
</head>

<div id="loading-overlay">
    <div id="loading-icon"></div>
</div>

<body>
    <div id="calendar">
        <?php
        // Inicializujeme prázdné pole pro seskupení dat podle roků
        $grouped_data = [];

        // Načteme data z databáze
        $result = $conn->query("SELECT * FROM zapis ORDER BY datum DESC");

        if ($result->num_rows > 0) {
            // Projdeme všechny záznamy
            while ($row = $result->fetch_assoc()) {
                $id_zapis = $row['id_zapis'];
                $datum = $row['datum'];
                $year = date('Y', strtotime($datum)); // Extrahujeme rok
        
                // Vytvoříme skupiny podle roku
                if (!isset($grouped_data[$year])) {
                    $grouped_data[$year] = []; // Pokud rok ještě neexistuje, vytvoříme prázdné pole
                }

                // Přidáme záznam do pole příslušného roku
                $grouped_data[$year][] = [
                    'id_zapis' => $id_zapis,
                    'datum' => date('d.m.Y', strtotime($datum))
                ];
            }

            // Vypíšeme data podle roku
            foreach ($grouped_data as $year => $items) {
                echo '<div class="year-container">';
                echo '<div class="table-heading"><b>';
                echo '&#x1F499;・Zápisy・' . $year;
                echo '</b></div>';
                echo '<div class="button-container">'; // Používáme tvůj existující styl pro tlačítka
                foreach ($items as $item) {
                    echo '<a href="./zapis.php?id_zapis=' . $item['id_zapis'] . '" target="_blank">';
                    echo '<button>';
                    echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . $item['datum'];
                    echo '</button>';
                    echo '</a>';
                }
                echo '</div>'; // Uzavřeme kontejner pro tlačítka
                echo '</div>'; // Uzavřeme kontejner pro rok
            }
        } else {
            echo "Žádná data nebyla nalezena.";
        }
        ?>

        <?php

        // Získání dat z tabulky
        $query = "SELECT * FROM other WHERE id_other = 2";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            // Kontrola hodnoty id
            if ($row['aktivni'] == 1) {
                $phpCode = $row['text'];

                // Vyhodnocení PHP kódu
                ob_start();
                eval ('?>' . $phpCode);
                $text = ob_get_clean();

                // Výpis HTML s dynamickým obsahem
                echo $text;
            } else {
            }
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }
        ?>
        <hr color="#3e6181" style="height: 2px; border: none;" />
        <?php

        // Získání dat z tabulky
        $query = "SELECT text FROM other WHERE id_other = 1";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $text = $row['text'];
            // Výpis HTML s dynamickým obsahem
            echo "$text";
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }
        ?>
    </div>
    <script src="./assets/js/script.js">
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
</body>

</html>