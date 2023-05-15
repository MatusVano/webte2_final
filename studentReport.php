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
    $query = "SELECT first_name, surname FROM account WHERE id =?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT test.*, file.source, file.points FROM test AS test INNER JOIN files as file ON test.file_id = file.id WHERE test.student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id']]);
    $student_tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

//    echo'<pre>';
////    var_dump($student);
//    var_dump($student_tests);
//    echo'</pre>';
} catch (PDOException $e){
    $err_msg .= "<p class='alert alert-danger' role='alert'>Nepodarilo sa načítať záznamy!</p>";
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
            <h2 class="text-center"><?php echo $student["first_name"] . " " . $student["surname"]; ?></h2>

            <?php
            foreach ($student_tests as $task) {
            ?>
            <div class="container mt-3 border border-3 border-dark rounded text-center">



                <h4>Zadanie:</h4>
                <span>
                    <?php
                    echo str_replace("$", "$$", $task["question"]);
                    if ($task["image"] != null) {
                        echo '<img src="uploads/images/' . basename($task["image"]) . '" class="img-fluid mt-5" alt="task image">';
                    }

                    ?>
                </span>

                <h4>Súbor:</h4>
                <p><?php echo str_replace("uploads/", "", $task['source']); ?></p>

                <h4>Odpoveď:</h4>
                <p><?php echo $task['answer']; ?></p>

                <h4>Správnosť Odpovede:</h4>
                <p><?php if ($task['points_gained'] > 0)
                        echo "Správny";
                    else
                        echo "Nesprávna"; ?></p>

                <h3>Počet získaných bodov:</h3>
                <p><?php echo $task['points_gained'] . "/" . $task['points']; ?></p>
            </div>
            <?php
            }
            ?>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/mathlive"></script>
    <script>
        window.addEventListener('DOMContentLoaded',
            () => MathLive.renderMathInDocument()
        );
    </script>
</body>
</html>