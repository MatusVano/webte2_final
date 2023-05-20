<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('config.php');
require_once('utils/parse_tex_file.php');

if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
}

if ($_SESSION["type"] != "Student") {
    header("location: index.php");
}

try {
    $id = $_GET['id'];
    $query = "SELECT t.question, t.image, t.answer, t.solution, t.points_gained, f.points FROM test t INNER JOIN files f ON t.file_id = f.id WHERE t.id = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($task)) {
        header("location: index.php");
    }

    if ($task["points_gained"] === NULL) {
        header("location: index.php");
    }

    unset($stmt);
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
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.3/r-2.4.0/datatables.min.css" />
    <link href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">

    <style>
        .container>span:nth-child(2)>span:nth-child(1)>span:nth-child(2) {
            display: inline-block !important;
            margin-inline: 5px;
        }
        section.container > span:nth-child(2) > span {
            display: inline-block !important;
            margin-inline: 5px;
        }
    </style>
</head>

<body>
    <?php
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        include 'components/loggedin_menu.php';
    } elseif (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        include 'components/loggedin_menu.php';
    } else {
        include 'components/loggedout_menu.php';
    }
    ?>

    <main>
        <section class="container mt-5 mb-5 w-50">
            <h2>Zadanie:</h2>
            <span>
                <?php
                $question = str_replace("$", "$$", $task["question"]);
                $question = str_replace("\begin{equation*}", "$$", $question);
                $question = str_replace("\\end{equation*}", "$$", $question);
                $question = str_replace("\\", "", $question);
                $question = str_replace("dfrac", "\dfrac", $question);
                echo $question;
                if ($task["image"] != null) {
                    echo '<img src="uploads/images/' . basename($task["image"]) . '" class="img-fluid mt-5" alt="task image">';
                }

                ?>
            </span>

            <h2 class="mt-5">Vaše riešenie:</h2>
            <span>
                <?php
                if ($task["answer"] != null) {
                    $answer = str_replace("$", "$$", $task["answer"]);
                    $answer = str_replace("\begin{equation*}", "$$", $answer);
                    $answer = str_replace("\\end{equation*}", "$$", $answer);

                    if (substr($answer, 0, 2) !== '$$') {
                        $answer = '$$' . $answer;
                    }
                    if (substr($answer, -2) !== '$$') {
                        $answer .= '$$';
                    }

                    echo $answer;
                } else {
                    echo "Neodovzdali ste žiadne riešenie.";
                }
                ?>
            </span>

            <h2 class="mt-5">Správne riešenie:</h2>
            <span>
                <?php
                echo str_replace("$", "$$", $task["solution"]);
                ?>
            </span>

            <h2 class="mt-5">Hodnotenie:</h2>
            <span>
                <?php
                echo $task["points_gained"] . "/" . $task["points"];
                ?>
            </span>
        </section>
    </main>

    <script src="https://unpkg.com/mathlive"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
        window.addEventListener('DOMContentLoaded',
            () => MathLive.renderMathInDocument()
        );
    </script>
</body>

</html>