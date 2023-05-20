<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../config.php";

// Get JSON as a string
$json_str = file_get_contents('php://input');

// Decode the JSON string into an associative array
$data = json_decode($json_str, true);

if (isset($data["id"])) {
    // if answer is not set, get it from db
    if (!isset($data["answer"])) {
        $data["answer"] = getAnswerFromDb($db, $data["id"]);

        if ($data["answer"] == "" || $data["answer"] == null) {
            echo json_encode(['status' => 'error', 'message' => 'Answer is empty.']);
            exit();
        }
    } else {
        if ($data["answer"] == "" || $data["answer"] == null) {
            echo json_encode(['status' => 'error', 'message' => 'Answer is empty.']);
            exit();
        }
    }

    $saved = saveAnswer($db, $data);

    if ($saved) {
        try {
            // get solution from db && max points
            $stmt1 = $db->prepare("SELECT t.solution, f.points FROM test t INNER JOIN files f ON t.file_id = f.id WHERE t.id = :task_id");
            $stmt1->bindParam(':task_id', $data["id"]);
            $stmt1->execute();
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
            $solution = $result["solution"];

            unset($stmt1);
            // check answer
            $is_correct = checkAns($solution, $data["answer"]);
            $final_points = $is_correct ? $result["points"] : 0;

            // save points to db
            $stmt2 = $db->prepare("UPDATE test SET points_gained = :points WHERE id = :task_id");
            $stmt2->bindParam(':task_id', $data["id"]);
            $stmt2->bindParam(':points', $final_points);
            $stmt2->execute();

            echo json_encode(['status' => 'ok', 'points' => $is_correct ? $result["points"] : 0]);
            unset($stmt2);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Answer not saved.']);
    }
}

function getAnswerFromDb($db, $id) {
    $stmt = $db->prepare("SELECT t.answer, t.solution, f.points FROM test t INNER JOIN files f ON t.file_id = f.id WHERE t.id = :task_id");
    $stmt->bindParam(':task_id', $id);
    $stmt->execute();
    $answerFromDb = $stmt->fetch(PDO::FETCH_ASSOC);

    unset($stmt);
    return $answerFromDb["answer"];
}

function saveAnswer($db, $data) {
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

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function checkAns($poly1, $poly2)
{
    $poly1 = latexToMaxima($poly1);
    $poly2 = latexToMaxima($poly2);
    
    $maxima_executable = '/usr/bin/maxima'; // cesta k maxime

    $maxima_command = sprintf('(%s) - (%s);', $poly1, $poly2);

    $maxima_output = shell_exec($maxima_executable . ' -b -q -r "' . $maxima_command . '"');
    $matches = [];
    if ($maxima_output != null) {
        preg_match('/\(%o\d+\)\s*(-?\d+\.?\d*)/', $maxima_output, $matches);
    }
    $outcome = isset($matches[1]) ? floatval($matches[1]) : null;

    if ($outcome == "0") {
        return true;
    } else {
        return false;
    }
}

function latexToMaxima($latex)
{
    $latex = str_replace('\begin{equation*}', '', $latex);
    $latex = str_replace('\end{equation*}', '', $latex);
    $latex = str_replace('\left', '', $latex);
    $latex = str_replace('\right', '', $latex);
    $latex = str_replace('\frac{', "(", $latex);
    $latex = str_replace("}{", ")/(", $latex);
    $latex = str_replace("}", ")", $latex);
    $latex = str_replace('\dfrac', '', $latex);
    $latex = str_replace('{', '(', $latex);
    $latex = str_replace('}', ')', $latex);
    $latex = str_replace('\\', '', $latex);
    $latex = str_replace('\cdot', '*', $latex);
    $latex = str_replace('cdot', '*', $latex);
    $latex = preg_replace('/([0-9]+)s/', '$1*s', $latex);
    $latex = preg_replace('/([0-9]+)t/', '$1*t', $latex);
    $latex = preg_replace('/([^a-zA-Z0-9_])e/', '$1*e', $latex);
    $latex = str_replace('(t-6)', '*(t-6)', $latex);
    $latex = str_replace('(t-4)', '*(t-4)', $latex);
    $latex = str_replace('(t-7)', '*(t-7)', $latex);
    //if found second = delete rest
    $latex = preg_replace('/=/', '', $latex, 1);
    // remove all spaces and new lines
    $latex = preg_replace('/\s+/', '', $latex);
    return $latex;
}
