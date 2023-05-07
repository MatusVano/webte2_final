<?php
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}


require_once('../config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $err_msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty(trim($_POST['first_name'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosim zadaj Meno</p>";
        if (empty(trim($_POST['surname'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosim zadaj Priezvisko</p>";
        if (empty(trim($_POST['password'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosim zadaj Heslo</p>";
        if (empty(trim($_POST['username'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosim zadaj prihlasovacie meno</p>";
        if (empty(trim($_POST['type'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosim zadaj Typ uctu</p>";

        if (!preg_match("/^[a-zA-Z]*$/",$_POST['first_name']))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Meno moze obsahovat iba Pismena</p>";
        if (!preg_match("/^[a-zA-Z]*$/",$_POST['surname']))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Priezvisko moze obsahovat iba Pismena</p>";
        if (!preg_match("/^[a-zA-Z0-9]*$/",$_POST['password']))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Heslo moze obsahovat iba Pismena a Cisla</p>";
        if (!preg_match("/^[a-zA-Z0-9]*$/",$_POST['username']))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Zly format prihlasovacie meno</p>";
        if ($_POST['type'] != "Teacher" && $_POST['type'] != "Student")
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Zly typ uctu</p>";


        if ($err_msg == ""){

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

                $err_msg = '<p class="p-3 mb-2 bg-success text-white">Teraz sa mozte prihlasit: <a class="btn btn-primary" href="index.php" role="button">Prihlas sa</a></p>';
            }
        }
    }
    unset($db);

?>



<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="../style.css" rel="stylesheet">
</head>
<body class="bg-secondary" style="--bs-bg-opacity: .2;">
    <nav class="navbar navbar-expand-lg navbar-light bg-secondary">
        <a class="navbar-brand ml-1" href="../index.php">Final</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="btn btn-outline-primary" href="../index.php">Final</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary ml-1" href="#">EMPTY</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary ml-1" href="#">EMPTY</a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-outline-primary ml-1" href="index.php">Prihlás sa</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-md">
        <h1>Registruj sa</h1>

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
                <label for="InputUsername" class="form-label">Prihlasovacie Meno: </label>
                <input type="text" name="username" class="form-control" id="InputUsername" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" required>
            </div>

            <div class="mb-3">
                <label for="AccountType" class="form-label">Typ Uctu: </label>
                <select name="type" id="AccountType" class="form-select">
                    <option value="Student" selected>Ziak</option>
                    <option value="Teacher">Ucitel</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="InputPassword" class="form-label">Heslo: </label>
                <p>Heslo musi obsahov jedno male a jedno velke pismeno, jedno cislo a musi mat aspon 8 znakov</p>
                <p>Heslo moze obsahovat iba Pismena a Cisla</p>
                <input type="password" name="password" class="form-control" id="InputPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Heslo musi obsahov jedno male a jedno velke pismeno, jedno cislo a musi mat aspon 8 znakov" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" required>
            </div>

            <button type="submit" class="btn btn-primary">Registuj sa</button>

        </form>
        <span>Uz mas ucet? <a href="index.php">Prihlas sa</a> </span>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>

<?php
    } catch (PDOException $e){
        echo $e->getMessage();
    }

?>