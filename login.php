<?php include './assets/php/config.php';
session_start();
ob_start();
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
    <style>
        .form-input-wrapper {
            position: relative;
        }

        .password-input {
            padding-right: 30px;
            width: 100%;
            box-sizing: border-box;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 32%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredUsername = $_POST["username"];
    $enteredPassword = $_POST["password"];

    // Připravíme SQL dotaz pro získání hesla a přístupu na základě uživatelského jména
    $stmt = $conn->prepare("SELECT idusers, password, parlament_access_admin FROM users_alba_rosa_parlament WHERE email = ?");
    $stmt->bind_param("s", $enteredUsername);
    $stmt->execute();
    $stmt->store_result();

    // Pokud najdeme uživatele, získáme jeho údaje
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($idusers, $hashedPassword, $parlamentAccess);
        $stmt->fetch();

        // Kontrola, zda má uživatel přístup do parlamentu
        if ($parlamentAccess !== '1') {
            $loginError = "Chybí oprávnění.";
        } else {
            // Ověření hesla pomocí password_verify
            if (password_verify($enteredPassword, $hashedPassword)) {
                $_SESSION['idusers'] = $idusers;
                header("Location: ./");
                exit();
            } else {
                $loginError = "Nesprávné přihlašovací údaje.";
            }
        }
    } else {
        $loginError = "Uživatel nenalezen.";
    }

    $stmt->close();
}





?>
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<body>
    <div id="calendar">
        <div class="overlay" id="overlay" onclick="closeAllMenus()"></div>
        <nav>


            <div class="user-icon" onclick="toggleUserMenu(event)">
                <i class="fa fa-user"></i>
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
                    <a href="./logout.php">Logout</a>
                <?php } else { ?>
                    <a href="./login.php">Login</a>
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
        if (isset($loginError)) {
            echo '<div class="error-message">';
            echo '<i class="fa fa-times" style="margin-right: 5px;"></i>' . $loginError;
            echo '</div>';
        }
        ?>
        <div class="table-heading">
            <h2> <?php echo $headerText; ?> </h2>
        </div>
        <div class="button-container" id="buttonContainer">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="loginForm"
                style="width: 80%; max-width: 400px; margin-bottom: 20px;">
                <label for="username" style="font-size: 16px; margin-bottom: 8px;">Uživatelské
                    jméno:</label>
                <input type="text" name="username" required
                    style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                <label for="password" style="font-size: 16px; margin-bottom: 8px;">Heslo:</label>
                <div class="form-input-wrapper">
                    <input type="password" name="password" id="txt_pwd" required
                        style="width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                    <span class="toggle-password" id="toggle_pwd" onclick="togglePassword()"><i
                            class="fa fa-eye"></i></span>
                </div>
                <button type="submit"><i class="fa fa-sign-in" aria-hidden="true"></i>
                    Přihlásit se
                </button>
            </form>
        </div>

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
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3BL123NWSE"></script>
    <script src="./assets/js/script.js"></script>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("txt_pwd");
            var icon = document.getElementById("toggle_pwd").querySelector("i");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
<?php
ob_end_flush();
?>