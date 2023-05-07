<?php

session_start();

require_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
</head>

<body class="bg-secondary" style="--bs-bg-opacity: .2;">
    <nav class="navbar navbar-expand-lg navbar-light bg-secondary">
        <a class="navbar-brand ml-1" href="index.php">Final</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="btn btn-primary" href="index.php">Final</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary ml-1" href="#">EMPTY</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary ml-1" href="#">EMPTY</a>
                </li>

                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){ ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary ml-1" href="#"><?php echo $_SESSION["first_name"] . " " . $_SESSION["surname"]; ?></a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-outline-primary ml-1" href="authentification/logout.php">Odhlas sa</a>
                    </li>
                <?php }else{ ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary ml-1" href="authentification/index.php">Prihlás sa</a>
                    </li>
                <?php }?>
            </ul>
        </div>
    </nav>


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