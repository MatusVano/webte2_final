<?php

require_once ("../config.php");

session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $err_msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (empty(trim($_POST['username'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosim zadaj prihlasovacie meno</p>";
        if (empty(trim($_POST['password'])))
            $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Prosim zadaj Heslo</p";

        if ($err_msg == ""){

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
                        $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Nespravne prihlasovacie meno alebo heslo.</p>";
                } else
                    $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Nespravne prihlasovacie meno alebo heslo.</p>";
            } else
                $err_msg .= "<p class='p-3 mb-2 bg-warning text-dark'>Ups. Nieco sa pokazilo!, Skus to prosim neskor</p>";

            unset($stmt);
            unset($db);
        }
    }
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
                    <a class="btn btn-primary ml-1" href="index.php">Prihlás sa</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-md">
        <h1>Prihlas sa</h1>

        <?php
        echo $err_msg;
        ?>

        <form action="#" method="post">
            <input type="hidden" name="type" value="classic">

            <div class="mb-3">
                <label for="InputUsername" class="form-label">Prihlasovacie Meno: </label>
                <input type="text" name="username" class="form-control" id="InputUsername" required>
            </div>

            <div class="mb-3">
                <label for="InputPassword" class="form-label">Heslo: </label>
                <input type="password" name="password" class="form-control" id="InputPassword" required>
            </div>

            <button type="submit" class="btn btn-primary">Prihlas sa</button>
        </form>

        <p>Este nemas ucet? <a href="register.php">Registruj sa</a></p>
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