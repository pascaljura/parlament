<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./style.css">
    <link rel="shortcut icon" href="./logo.ico" type="image/x-icon"> 
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
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
        <div class="table-heading">
            <b><?php echo $headerText; ?></b>
        </div>
        <div class="button-container" id="buttonContainer">
            <?php
            include 'config.php';
            $result = $conn->query("SELECT id, datum FROM zapis ORDER BY datum DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $datum = date('d.m.Y', strtotime($row['datum']));
                    echo '<a href="./zapis.php?id=' . $id . '" target="_blank">';
                    echo '<button>';
                    echo '<i class="fa fa-file-pdf-o pdf-icon" aria-hidden="true"></i> ' . $datum;
                    echo '</button>';
                    echo '</a>';
                }
            } else {
                echo "Žádná data nebyla nalezena.";
            }
            $conn->close();
            ?>
        </div>
        <?php
        include 'config.php'; // Připojení k databázi
        
        // Získání dat z tabulky
        $query = "SELECT * FROM other WHERE id = 2";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            // Kontrola hodnoty id
            if ($row['aktivni'] == 1) {
                $phpCode = $row['text'];

                // Vyhodnocení PHP kódu
                ob_start();
                eval('?>' . $phpCode);
                $text = ob_get_clean();

                // Výpis HTML s dynamickým obsahem
                echo $text;
            } else {
            }
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }

        // Uzavření připojení k databázi
        mysqli_close($conn);
        ?>





        <hr color="#3e6181" style="height: 2px; border: none;" />
        <?php
        include 'config.php'; // Připojení k databázi
        
        // Získání dat z tabulky
        $query = "SELECT text FROM other WHERE id = 1";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $text = $row['text'];

            // Výpis HTML s dynamickým obsahem
            echo "$text";
        } else {
            echo 'Chyba při získávání dat z databáze: ' . mysqli_error($conn);
        }

        // Uzavření připojení k databázi
        mysqli_close($conn);
        ?>
    </div>
    <script src="./script.js">
    </script>
</body>

</html>