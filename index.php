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
<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/favicon.ico" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <?php
    $headerText2 = '<i class="fa fa-heart blue"></i>・O nás';
    ?>
</head>
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<body>
    <div id="calendar">

        <?php

        // Získání dat z tabulky
        $query = "SELECT * FROM other_alba_rosa_parlament WHERE idother_parlament = 2";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            // Kontrola hodnoty id
            if ($row['active'] == 1) {
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
        <div class="table-heading">
            <h2> <?php echo $headerText2; ?> </h2>
        </div>
        <p>
            Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a podílejí se
            na chodu školy. Jeho hlavní funkce jsou:<br>
        <ul>
            <li><b>Zastupování žáků</b>: Předává názory a přání studentů vedení školy.</li>
            <li><b>Řešení problémů</b>: Pomáhá řešit otázky týkající se školního prostředí.</li>
            <li> <b>Organizace akcí</b>: Podílí se na plánování soutěží, tematických dnů nebo charitativních
                sbírek.</li>
            <li> <b>Spolupráce s vedením</b>: Pravidelně komunikuje s ředitelem a učiteli.</li>
        </ul>
        Zapojení do parlamentu rozvíjí komunikační a organizační dovednosti, umožňuje ovlivňovat dění ve škole a
        být v obraze ohledně plánovaných změn nebo akcí. Je to příležitost pro studenty, kteří chtějí něco
        změnit a aktivně se podílet na životě školy.


        </p>
        <?php

        // Inicializujeme prázdné pole pro seskupení dat podle roků
        $grouped_data = [];

        // Načteme data z databáze
        $result = $conn->query("SELECT * FROM notes_alba_rosa_parlament ORDER BY date DESC");

        if ($result->num_rows > 0) {
            // Projdeme všechny záznamy
            while ($row = $result->fetch_assoc()) {
                $idnotes_parlament = $row['idnotes_parlament'];
                $date = $row['date'];
                $year = date('Y', strtotime($date)); // Extrahujeme rok
        
                // Vytvoříme skupiny podle roku
                if (!isset($grouped_data[$year])) {
                    $grouped_data[$year] = []; // Pokud rok ještě neexistuje, vytvoříme prázdné pole
                }

                // Přidáme záznam do pole příslušného roku
                $grouped_data[$year][] = [
                    'idnotes_parlament' => $idnotes_parlament,
                    'date' => date('d.m.Y', strtotime($date))
                ];
            }

            // Vypíšeme data podle roku
            foreach ($grouped_data as $year => $items) {
                echo '<div class="year-container">';
                echo '<div class="table-heading"><h3>';
                echo '<i class="fa fa-heart blue"></i>・Zápisy・' . $year;
                echo '</h3></div>';
                echo '<div class="button-container">'; // Používáme tvůj existující styl pro tlačítka
                foreach ($items as $item) {
                    echo '<a href="./show_notes.php?idnotes_parlament=' . $item['idnotes_parlament'] . '" target="_blank">';
                    echo '<button>';
                    echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . $item['date'];
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
        <hr color="black" style="height: 2px; border: none;" />
        <?php

        // Získání dat z tabulky
        $query = "SELECT text FROM other_alba_rosa_parlament WHERE idother_parlament = 1";
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