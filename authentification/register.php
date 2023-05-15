<?php
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}


require_once('../config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    $err_msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty(trim($_POST['first_name'])))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Prosím zadaj Meno!</p>";
        if (empty(trim($_POST['surname'])))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Prosím zadaj Priezvisko!</p>";
        if (empty(trim($_POST['password'])))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Prosím zadaj Heslo!</p>";
        if (empty(trim($_POST['username'])))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Prosím zadaj Prihlasovacie meno!</p>";
        if (empty(trim($_POST['type'])))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Prosím zadaj Typ účtu!</p>";

        if (!preg_match("/^[a-zA-Z]*$/", $_POST['first_name']))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Meno môže obsahovať iba písmená!</p>";
        if (!preg_match("/^[a-zA-Z]*$/", $_POST['surname']))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Priezvisko môže obsahovať iba písmená!</p>";
        if (!preg_match("/^[a-zA-Z0-9]*$/", $_POST['password']))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Heslo moze obsahovat iba písmená a čísla!</p>";
        if (!preg_match("/^[a-zA-Z0-9]*$/", $_POST['username']))
            $err_msg .= "<p class='alert alert-danger' role='alert'>Zlý formát prihlasovacieho mena!</p>";
        if ($_POST['type'] != "Teacher" && $_POST['type'] != "Student")
            $err_msg .= "<p class='alert alert-danger' role='alert'>Zlý typ účtu!</p>";


        if ($err_msg == "") {

            $sql = "SELECT id, username, password FROM account WHERE username = ?";
            $stmt = $db->prepare($sql);
            $success = $stmt->execute([$_POST['username']]);

            if ($stmt->rowCount() == 0) {

                $sql = "INSERT INTO account (username, password, type, first_name, surname) VALUES (:username, :password, :type, :first_name, :surname)";
                $hashed_password = password_hash($_POST['password'], PASSWORD_ARGON2ID);

                $stmt = $db->prepare($sql);

                $stmt->bindParam(":username", $_POST['username'], PDO::PARAM_STR);
                $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
                $stmt->bindParam(":type", $_POST['type'], PDO::PARAM_STR);
                $stmt->bindParam(":first_name", $_POST['first_name'], PDO::PARAM_STR);
                $stmt->bindParam(":surname", $_POST['surname'], PDO::PARAM_STR);
                $stmt->execute();

                unset($stmt);

                $err_msg = '<p class="alert alert-success" role="alert">Teraz sa môžete prihlásiť: <a class="btn btn-primary" href="index.php" role="button">Prihlásiť sa</a></p>';
            }
        }
    }
    unset($db);
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
    <link href="../style.css" rel="stylesheet">
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
        <h1>Zaregistruj sa</h1>

        <?php
        echo $err_msg;
        ?>

        <form action="register.php" method="post">
            <div class="mb-3">
                <label for="InputFirstname" class="form-label">Meno: </label>
                <input type="text" name="first_name" class="form-control" id="InputFirstname" onkeydown="return /[a-zA-Z]/i.test(event.key)" required>
            </div>

            <div class="mb-3">
                <label for="InputSurname" class="form-label">Priezvisko: </label>
                <input type="text" name="surname" class="form-control" id="InputSurname" onkeydown="return /[a-zA-Z]/i.test(event.key)" required>
            </div>

            <div class="mb-3">
                <label for="InputUsername" class="form-label">Prihlasovacie meno: </label>
                <input type="text" name="username" class="form-control" id="InputUsername" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" required>
            </div>

            <div class="mb-3">
                <label for="AccountType" class="form-label">Typ účtu: </label>
                <select name="type" id="AccountType" class="form-select">
                    <option value="Student" selected>Žiak</option>
                    <option value="Teacher">Učiteľ</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="InputPassword" class="form-label">Heslo: </label>
                <p>Heslo musí obsahovať jedno malé a jedno veľké písmeno, jedno číslo a musí mať aspoň 8 znakov!</p>
                <p>Heslo môže obsahovať iba písmená a čísla!</p>
                <input type="password" name="password" class="form-control" id="InputPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Heslo musi obsahov jedno male a jedno velke pismeno, jedno cislo a musi mat aspon 8 znakov" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" required>
            </div>
            <p>Už máš účet? <a href="index.php">Prihlás sa</a></p>
            <button type="submit" class="btn btn-primary">Zaregistrovať sa</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

</html>