<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('config.php');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false || $_SESSION["type"] !== "Teacher") {
    header("location: index.php");
    exit;
}

$err_msg = "";
//sudo chown -R www-data:www-data /var/www
//sudo chmod -R g+rwX /var/www

try {
    if (isset($_POST['active'])) {
        if (isset($_FILES['my_file']['name']) && ($_FILES['my_file']['name'] != "")) {
            // Where the file is going to be stored
            $target_dir = "uploads/";
            $file = $_FILES['my_file']['name'];
            $path = pathinfo($file);
            $filename = $path['filename'];
            $ext = $path['extension'];
            $temp_name = $_FILES['my_file']['tmp_name'];
            $path_filename_ext = $target_dir . $filename . "." . $ext;

            // Check if file already exists
            if (file_exists($path_filename_ext)) {
                $err_msg = "<p class='alert alert-danger mt-3 mb-3' role='alert'>Súbor už existuje!</p>";
            } else {
                move_uploaded_file($temp_name, $path_filename_ext);

                $sql = "INSERT INTO files (active, active_end, source, points) VALUES (:active, :active_end, :source, :points)";

                $stmt = $db->prepare($sql);

                $source = "uploads/" . $file;

                $stmt->bindParam(":active", $_POST['active'], PDO::PARAM_STR);
                $stmt->bindParam(":active_end", $_POST['active_end'], PDO::PARAM_STR);
                $stmt->bindParam(":source", $source, PDO::PARAM_STR);
                $stmt->bindParam(":points", $_POST['points'], PDO::PARAM_STR);
                $stmt->execute();

                unset($stmt);

                $err_msg = "<p class='alert alert-success mt-3 mb-3' role='alert'>Súbor bol úspešne nahraný!</p>";
            }
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
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

<body>
    <?php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        if (isset($_SESSION["type"]) && $_SESSION["type"] == "Student") {
            include 'components/loggedin_menu.php';
        } elseif (isset($_SESSION["type"]) && $_SESSION["type"] == "Teacher") {
            include 'components/loggedin_teacher_menu.php';
        } else {
            include 'components/loggedout_menu.php';
        }
    } elseif (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        if (isset($_SESSION["type"]) && $_SESSION["type"] == "Student") {
            include 'components/loggedin_menu.php';
        } elseif (isset($_SESSION["type"]) && $_SESSION["type"] == "Teacher") {
            include 'components/loggedin_teacher_menu.php';
        } else {
            include 'components/loggedout_menu.php';
        }
    } else {
        include 'components/loggedout_menu.php';
    }
    ?>

    <div class="container mt-5 w-25">
        <h1>Nahrať súbor</h1>

        <?php
        echo $err_msg;
        ?>

        <form name="form" action="#" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="InputActive" class="form-label">Aktívny? </label>
                <select name="active" id=InputActive" class="form-select">
                    <option value="1" selected>ÁNO</option>
                    <option value="0">NIE</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ActiveEnd" class="form-label">Dokedy má byť súbor aktívny? </label>
                <input type="date" name="active_end" class="form-control" id="ActiveEnd" required>
            </div>

            <div class="mb-3">
                <label for="InputFile" class="form-label">Vybrať súbor: </label>
                <input type="file" name="my_file" class="form-control" id="InputFile" required>
            </div>

            <div class="mb-3">
                <label for="InputPoints" class="form-label">Počet bodov za príklad: </label>
                <input type="number" name="points" class="form-control" id="InputPoints" required>
            </div>

            <button type="submit" class="btn btn-primary">Nahrať súbor</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>