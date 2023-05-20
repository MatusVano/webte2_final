<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../config.php");

session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

try {
    $err_msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty(trim($_POST['username'])))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Prosím zadaj prihlasovacie meno!</p>";
        if (empty(trim($_POST['password'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosím zadaj heslo!</p";

        if ($err_msg == "") {

            $sql = "SELECT * FROM account WHERE username = :username";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":username", $_POST["username"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    // Uzivatel existuje, skontroluj heslo.
                    $row = $stmt->fetch();
                    $hashed_password = $row["password"];
                    
                    if (password_verify($_POST['password'], $hashed_password)) {
                        // Heslo je spravne.

                        // Uloz data pouzivatela do session.
                        $_SESSION["loggedin"] = true;
                        $_SESSION["user_id"] = $row['id'];
                        $_SESSION["username"] = $row['username'];
                        $_SESSION["type"] = $row['type'];
                        $_SESSION["first_name"] = $row['first_name'];
                        $_SESSION["surname"] = $row['surname'];

                        // Presmeruj pouzivatela na zabezpecenu stranku.
                        header("location: ../index.php");
                    } else
                        $err_msg .= "<p class='alert alert-danger' role='alert'>Nespravne prihlasovacie meno alebo heslo.</p>";
                } else
                    $err_msg .= "<p class='alert alert-danger' role='alert'>Nespravne prihlasovacie meno alebo heslo.</p>";
            } else
                $err_msg .= "<p class='alert alert-danger' role='alert'>Ups. Nieco sa pokazilo!, Skus to prosim neskor</p>";

            unset($stmt);
            unset($db);
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Semestrálne zadanie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="../style.css">
</head>

<body>
    <?php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        include '../components/loggedin_menu.php';
    } elseif (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        include '../components/loggedin_menu.php';
    } else {
        include '../components/loggedout_menu.php';
    }
    ?>

    <div class="container w-25 mt-5">
        <h1>Prihlás sa</h1>

        <?php
        echo $err_msg;
        ?>

        <form action="#" method="post">
            <input type="hidden" name="type" value="classic">

            <div class="mb-3">
                <label for="InputUsername" class="form-label">Prihlasovacie meno: </label>
                <input type="text" name="username" class="form-control" id="InputUsername" required>
            </div>

            <div class="mb-3">
                <label for="InputPassword" class="form-label">Heslo: </label>
                <input type="password" name="password" class="form-control" id="InputPassword" required>
            </div>
            <p>Ešte nemáš účet? <a href="register.php">Zaregistruj sa</a></p>
            <button type="submit" class="btn btn-primary">Prihlásiť sa</button>
        </form>
    </div>



    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

</html>