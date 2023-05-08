<?php

session_start();

require_once('config.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false || $_SESSION["type"] !== "Teacher"){
    header("location: index.php");
    exit;
}

$err_msg = "";

//sudo chown -R www-data:www-data /var/www
//sudo chmod -R g+rwX /var/www

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['active'])){
        if (isset($_FILES['my_file']['name']) && ($_FILES['my_file']['name']!="")){
            // Where the file is going to be stored
            $target_dir = "uploads/";
            $file = $_FILES['my_file']['name'];
            $path = pathinfo($file);
            $filename = $path['filename'];
            $ext = $path['extension'];
            $temp_name = $_FILES['my_file']['tmp_name'];
            $path_filename_ext = $target_dir.$filename.".".$ext;

            // Check if file already exists
            if (file_exists($path_filename_ext)) {
                $err_msg = "<p class='p-3 mb-2 bg-warning text-dark'>Subor uz existuje.</p>";
            }else{
                move_uploaded_file($temp_name,$path_filename_ext);

                $sql = "INSERT INTO files (active, active_end, source, points) VALUES (:active, :active_end, :source, :points)";

                $stmt = $db->prepare($sql);

                $source = "uploads/" . $file;

                $stmt->bindParam(":active", $_POST['active'], PDO::PARAM_STR);
                $stmt->bindParam(":active_end", $_POST['active_end'], PDO::PARAM_STR);
                $stmt->bindParam(":source", $source, PDO::PARAM_STR);
                $stmt->bindParam(":points", $_POST['points'], PDO::PARAM_STR);
                $stmt->execute();

                unset($stmt);

                $err_msg = '<p class="p-3 mb-2 bg-success text-white"> Subor nahraty Uspesne.</p>';

            }
        }
    }

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
                    <a class="btn btn-outline-primary" href="index.php">Final</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary ml-1" href="#">EMPTY</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-primary ml-1" href="#">EMPTY</a>
                </li>

                <?php
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                    if($_SESSION["type"] === "Teacher"){ ?>
                        <li class="nav-item">
                            <a class="btn btn-primary ml-1" href="uploadFile.php">Nahraj Subor</a>
                        </li>
                        <?php
                    }
                    ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary ml-1" href="#"><?php echo $_SESSION["first_name"] . " " . $_SESSION["surname"]; ?></a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-outline-primary ml-1" href="authentification/logout.php">Odhlas sa</a>
                    </li>
                    <?php
                }else{ ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary ml-1" href="authentification/index.php">Prihlás sa</a>
                    </li>
                <?php }?>
            </ul>
        </div>
    </nav>

    <div class="container-md">
        <h1>Nahraj Subor</h1>

        <?php
        echo $err_msg;
        ?>

        <form name="form" action="#" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="InputActive" class="form-label">Aktivny?: </label>
                <select name="active" id=InputActive" class="form-select">
                    <option value="1" selected>ANO</option>
                    <option value="0">NIE</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ActiveEnd" class="form-label">Dokedy je subor aktivny?: </label>
                <input type="date" name="active_end" class="form-control" id="ActiveEnd" required>
            </div>

            <div class="mb-3">
                <label for="InputFile" class="form-label">Subor: </label>
                <input type="file" name="my_file" class="form-control" id="InputFile" required>
            </div>

            <div class="mb-3">
                <label for="InputPoints" class="form-label">Pocet Bodov: </label>
                <input type="number" name="points" class="form-control" id="InputPoints" required>
            </div>

            <button type="submit" class="btn btn-primary">Nahraj Subor</button>
        </form>
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