<?php include './assets/php/config.php';
session_start();
ob_start();

if (isset($_SESSION['idusers_parlament'])) {
    // Uživatel nenalezen (může být smazán), odhlásíme ho
    header("Location: ./");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <title>Alba-rosa.cz | Parlament na Purkyňce</title>
    <link rel="manifest" href="./assets/json/manifest.json">
    <link rel="stylesheet" href="./assets/css/style.css">

    <!-- OG Metadata -->
    <meta property="og:title" content="Alba-rosa.cz | Parlament na Purkyňce" />
    <meta property="og:url" content="https://www.alba-rosa.cz/parlament/" />
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
    $enteredemail = $_POST["email"];
    $enteredPassword = $_POST["password"];

    $stmt = $conn->prepare("SELECT idusers_parlament, password, parlament_access_admin, parlament_access_user FROM users_alba_rosa_parlament WHERE email = ?");
    $stmt->bind_param("s", $enteredemail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($idusers_parlament, $hashedPassword, $parlamentAccessAdmin, $parlamentAccessUser);
        $stmt->fetch();

        if ($parlamentAccessAdmin != '1' && $parlamentAccessUser != '1') {
            $loginError = "Chybí oprávnění.";
        } else {
            if (password_verify($enteredPassword, $hashedPassword)) {
                $_SESSION['idusers_parlament'] = $idusers_parlament;

                if ($parlamentAccessAdmin == '1') {
                    $_SESSION['parlament_role'] = 'admin';
                } elseif ($parlamentAccessUser == '1') {
                    $_SESSION['parlament_role'] = 'user';
                }

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
        <?php
        if (isset($loginError)) {
            echo '<div class="error-message">';
            echo '<i class="fa fa-times" style="margin-right: 5px;"></i>' . $loginError;
            echo '</div>';
        }
        ?>
        <div class="table-heading">
            <h2> <i class="fa fa-heart blue"></i>・Přihlášení</h2>
        </div>
        <div class="button-container" id="buttonContainer">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="loginForm"
                style="width: 80%; max-width: 400px; margin-bottom: 20px;">
                <label for="email" style="font-size: 16px; margin-bottom: 8px;">Email:</label>
                <input type="text" name="email" required
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