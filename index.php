<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('config.php');
require_once('utils/parse_tex_file.php');
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
		.form-select {
			display: inline-block;
			width: auto;
		}

		.form-control {
			display: inline-block;
			width: auto;
			margin-left: .4rem;
		}

		.dataTables_filter {
			text-align: right;
		}
	</style>
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

    <?php
        if(isset($_SESSION["type"]) && $_SESSION["type"] == "Student") {
            include 'components/student_index.php';
        } elseif (isset($_SESSION["type"]) && $_SESSION["type"] == "Teacher") {
            include 'components/teacher_index.php';
        } else {
            header("location: authentification/index.php");
        }
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>