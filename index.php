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
    $headerText2 = '&#x1F499;・O nás';
    ?>
</head>
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<body>
    <div id="calendar">

        <?php

        // Získání dat z tabulky
        $query = "SELECT * FROM other_alba_rosa_parlament WHERE idother = 2";
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
        <div class="table-heading">
            <b> <?php echo $headerText2; ?> </b>
        </div>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin consectetur fringilla sapien, at dapibus
            est. Fusce nec leo eu arcu convallis ultrices in sit amet leo. Cras rhoncus feugiat purus eu cursus.
            Nullam eget tempus dui. Donec nibh nibh, sollicitudin a posuere congue, ultricies quis tellus.
            Pellentesque dui ex, sollicitudin sed tincidunt vel, eleifend vel urna. Nulla facilisi. Sed odio dolor,
            consequat sit amet aliquet non, faucibus quis lacus. Fusce dui orci, eleifend non dictum eu, sodales
            eget mi. Integer sed rhoncus odio, a venenatis ipsum. Cras id vehicula nibh, tincidunt mattis augue.
            Aenean volutpat odio et arcu blandit commodo. Vestibulum blandit sagittis magna a tristique. Ut porta
            sit amet ex eget tempor. <br><br>

            Integer elementum rutrum tincidunt. Vivamus a pharetra quam. Vivamus sit amet enim ut sem lobortis
            blandit. Nunc at leo mauris. Maecenas congue sem ante, a imperdiet lacus porttitor non. Integer
            vestibulum tristique velit, id eleifend dui. Cras fringilla orci a nibh cursus blandit. Mauris ac libero
            faucibus, cursus quam volutpat, blandit sem. Phasellus vestibulum in odio vitae interdum. Fusce sem
            dolor, blandit a ullamcorper in, eleifend eget erat. In dolor tortor, scelerisque eu massa non,
            malesuada blandit urna. Duis hendrerit eleifend pellentesque. Nullam varius erat non erat maximus, sed
            fermentum felis pulvinar. Vestibulum et nunc neque.<br><br>

            Vivamus ut risus sem. Sed vitae leo arcu. Praesent condimentum tristique arcu vitae aliquam. Aliquam
            accumsan massa ac diam pellentesque, non mattis odio tristique. Etiam sit amet dignissim felis. Nam orci
            metus, scelerisque a odio rhoncus, hendrerit porttitor risus. Orci varius natoque penatibus et magnis
            dis parturient montes, nascetur ridiculus mus. Duis iaculis dui et diam hendrerit porta. Nam at diam a
            neque dignissim tristique faucibus id massa.<br><br>

            Aliquam erat volutpat. Curabitur ullamcorper ultrices porta. In ultricies bibendum semper. Pellentesque
            varius vehicula luctus. Etiam dignissim nisl vel sem auctor laoreet. Suspendisse quis augue efficitur,
            cursus leo in, dignissim diam. Proin metus quam, finibus semper eleifend sed, sollicitudin et dolor.
            Aliquam sit amet nulla id elit viverra venenatis a vel felis. In accumsan rutrum nisi ac mollis.
            Pellentesque condimentum rutrum ante, ac placerat diam consequat ac. Quisque dictum ex at quam maximus
            fermentum. Sed tristique sodales consequat. Morbi eget dolor ac arcu varius tincidunt non sed turpis.
            Aenean ut varius mi, eu mollis metus. Donec tristique imperdiet enim, non dictum turpis scelerisque at.
            <br><br>
            Nullam aliquet fermentum ex, nec euismod urna venenatis eu. Nulla facilisi. Lorem ipsum dolor sit amet,
            consectetur adipiscing elit. In ac velit ac sapien cursus facilisis. Sed at ultrices purus. Mauris vitae
            eros eget lectus faucibus semper non nec arcu. Nam turpis mauris, molestie nec augue in, bibendum tempor
            mi. Vivamus porta mattis nisl ac commodo. Nulla finibus tincidunt blandit. Donec a nulla placerat tortor
            euismod blandit. Mauris vitae dignissim felis, in tincidunt dui. Fusce a sagittis augue.


        </p>
        <?php

        // Inicializujeme prázdné pole pro seskupení dat podle roků
        $grouped_data = [];

        // Načteme data z databáze
        $result = $conn->query("SELECT * FROM zapis_alba_rosa_parlament ORDER BY datum DESC");

        if ($result->num_rows > 0) {
            // Projdeme všechny záznamy
            while ($row = $result->fetch_assoc()) {
                $idzapis = $row['idzapis'];
                $datum = $row['datum'];
                $year = date('Y', strtotime($datum)); // Extrahujeme rok
        
                // Vytvoříme skupiny podle roku
                if (!isset($grouped_data[$year])) {
                    $grouped_data[$year] = []; // Pokud rok ještě neexistuje, vytvoříme prázdné pole
                }

                // Přidáme záznam do pole příslušného roku
                $grouped_data[$year][] = [
                    'idzapis' => $idzapis,
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
                    echo '<a href="./show_zapis.php?idzapis=' . $item['idzapis'] . '" target="_blank">';
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
        <hr color="black" style="height: 2px; border: none;" />
        <?php

        // Získání dat z tabulky
        $query = "SELECT text FROM other_alba_rosa_parlament WHERE idother = 1";
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