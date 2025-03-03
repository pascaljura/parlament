<?php include './assets/php/config.php';
session_start();
ob_start();

if (isset($_SESSION['idusers'])) {
    $userId = $_SESSION['idusers'];

    $stmt = $conn->prepare("SELECT * FROM users_alba_rosa_parlament WHERE idusers = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($userData = $result->fetch_assoc()) {
        // Uložení do proměnných
        $idusers_parlament = $userData['idusers'];
        $email_parlament = $userData['email'];
        $username_parlament = $userData['username'];
        $parlament_access_admin = $userData['parlament_access_admin'];
        $parlament_access_user = $userData['parlament_access_user'];
        // Nové sloupce (práva a přístupy)
        $add_notes = $userData['add_notes'];
        $delete_notes = $userData['delete_notes'];
        $edit_notes = $userData['edit_notes'];
        $start_attendances = $userData['start_attendances'];
        $end_attendances = $userData['end_attendances'];
        $delete_attendances = $userData['delete_attendances'];
        $qr_attendances = $userData['qr_attendances'];
        $select_idnotes_parlament = $userData['select_idnotes_parlament'];
        $show_attendances = $userData['show_attendances'];


    } else {
        // Uživatel nenalezen (může být smazán), odhlásíme ho
        header("Location: ./logout.php");
        exit();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <meta content="Alba-rosa.cz | Parlament na Purkyňce" property="og:title" />
    <meta content="https://www.alba-rosa.cz/" property="og:url" />
    <meta content="https://www.alba-rosa.cz/parlament/logo.png" property="og:image" />
    <meta content="#0f1523" data-react-helmet="true" name="theme-color" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap" rel="stylesheet">

    <?php
    $headerText = '<i class="fa fa-heart blue"></i>・Přihlášení';
    $headerText2 = '<i class="fa fa-heart blue"></i>・O nás';
    ?>
</head>

<div id="loading-overlay">
    <div class="loader"></div>
</div>

<body>
    <div id="calendar">
        <div class="overlay" id="overlay" onclick="closeAllMenus()"></div>
        <nav>


            <div class="user-icon" onclick="toggleUserMenu(event)">
                                <?php if (!empty($username_parlament)) { ?>
<i class="fa fa-user" style="color: #70B95E;"></i>
  <?php } else { ?>
<i class="fa fa-user" style="color: #3C3C3B;"></i>
  <?php } ?>
            </div>



            <!-- Navigation Links (vlevo na PC) -->
            <div class="nav-links">
                <a href="./" class="active">Domů</a>
                <a href="./notes">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                    <a href="./attendances">Prezenční listiny</a>
                <?php } ?>
            </div>

            <!-- Hamburger Menu Icon (vpravo na mobilu) -->
            <div class="hamburger" onclick="toggleMobileMenu(event)">
                <i class="fa fa-bars"></i>
            </div>

            <!-- User Dropdown Menu -->
            <div class="user-dropdown" id="userDropdown">
                <?php if (!empty($username_parlament)) { ?>
                    <p>Přihlášen jako: <b><?php echo $username_parlament; ?></b></p>
                    <a href="./logout.php">Odhlásit se</a>
                <?php } else { ?>
                    <a href="./login.php">Přihlásit se</a>
                <?php } ?>
            </div>


            <!-- Mobile Menu -->
            <div class="mobile-menu" id="mobileMenu">
                <a href="./" class="active">Domů</a>
                <a href="./notes">Zápisy</a>
                <?php if (isset($show_attendances) && $show_attendances == '1') { ?>
                <a href="./attendances">Prezenční listiny</a>
                <?php } ?>
            </div>
        </nav>
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

            // Výstup zprávy s ikonou a třídou
            echo '<div onclick="removeQueryString()" class="' . $message_class . '" style="cursor: pointer;">';
            echo '<i class="fa ' . $message_icon . '" style="margin-right: 5px;"></i> ' . htmlspecialchars($message);
            echo '</div>';
        }


        ?>

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
            Školní parlament na Purkyňce je skupina studentů z každé třídy, kteří zastupují zájmy žáků a
            podílejí se
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


        </p><br>




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
</body>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
<script src="./assets/js/script.js">
</script>

</html>
<?php
ob_end_flush();
?>