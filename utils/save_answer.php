<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../config.php";

// Get JSON as a string
$json_str = file_get_contents('php://input');

// Decode the JSON string into an associative array
$data = json_decode($json_str, true);

// Save answer from json to db
if (isset($data["id"]) && isset($data["answer"])) {
    $task_id = $data["id"];
    $answer = $data["answer"];

    if ($answer == "") {
        $answer = null;
    }

    try {
        $stmt = $db->prepare("UPDATE test SET answer = :answer WHERE id = :task_id");
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':answer', $answer);
        $stmt->execute();

        $response_data = ['status' => 'ok'];
        header('Content-Type: application/json');
        echo json_encode($response_data);
    } catch (PDOException $e) {
        $response_data = ['status' => 'error', 'message' => $e->getMessage()];
        header('Content-Type: application/json');
        echo json_encode($response_data);
    }
}
