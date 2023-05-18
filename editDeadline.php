<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!(isset($_SESSION["type"]) && $_SESSION["type"] == "Teacher")) {
    header("location: index.php");
}

require_once('config.php');
require_once('utils/parse_tex_file.php');

$err_msg = "";

try {
    $query = "SELECT * FROM files";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['file_id'])){
        $sql = "UPDATE files SET active=?, active_end=?, points=? where id=?";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([$_POST["active"], $_POST["active_end"], $_POST["points"], $_POST["file_id"]]);
        $err_msg .= "<p class='alert alert-success' role='alert'>Úprava prebehla úspešne!</p>";

    }
} catch (PDOException $e){
    $err_msg .= "<p class='alert alert-danger' role='alert'>Niečo sa pokazilo!</p>";
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
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.3/r-2.4.0/datatables.min.css" />
    <link href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">

    <style>
        .container>span:nth-child(2)>span:nth-child(1)>span:nth-child(2) {
            display: inline-block !important;
            margin-inline: 5px;
        }
    </style>
</head>

<body>
<?php
include 'components/loggedin_teacher_menu.php';
?>

<main class="mb-5">
    <section class="container w-25 mt-5">
        <?php
        echo $err_msg;
        ?>
    </section>

    <section class="container w-50 mt-5">
        <form name="form" action="#" method="post" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="InputFile" class="form-label">Vybrať súbor: </label>
                <select name="file_id" id="InputFile" class="form-select">
                    <?php
                    foreach ($files as $file){
                        echo '<option value="' . $file['id'] . '">' . str_replace("uploads/", "", $file['source']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="InputActive" class="form-label">Aktívny? </label>
                <select name="active" id="InputActive" class="form-select">
                    <option value="1" selected>ÁNO</option>
                    <option value="0">NIE</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ActiveEnd" class="form-label">Dokedy má byť súbor aktívny? </label>
                <input type="date" name="active_end" class="form-control" id="ActiveEnd" required>
            </div>

            <div class="mb-3">
                <label for="Points" class="form-label">Maximálny počet bodov </label>
                <input type="number" name="points" class="form-control" id="Points" required>
            </div>

            <button type="submit" class="btn btn-primary">Upraviť súbor</button>
        </form>

    </section>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script src="https://unpkg.com/mathlive"></script>
</body>
</html>