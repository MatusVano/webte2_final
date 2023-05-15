<?php
$err_msg = "";

// Generate exercises
if (!empty($_POST) && !empty($_POST["user_id"]) && !empty($_POST["files"])) {
    $parsed_files = array();

    foreach ($_POST["files"] as $file) {
        $parsed_files[] = array(
            "id" => explode(":", $file)[0],
            "source" => explode(":", $file)[1],
            "tasks" => parseFile($file)
        );
    }

    // save tasks to db
    try {
        $stmt = $db->prepare("INSERT INTO test (student_id, question, image, solution, file_id) VALUES (:user_id, :task, :image, :solution, :file_id)");

        foreach ($parsed_files as $file) {
            foreach ($file["tasks"] as $task) {
                $stmt->bindParam(':user_id', $_POST["user_id"]);
                $stmt->bindParam(':task', $task["task"]);
                $stmt->bindParam(':image', $task["image"]);
                $stmt->bindParam(':solution', $task["solution"]);
                $stmt->bindParam(':file_id', $file["id"]);
                $stmt->execute();
            }
        }

        // refresh page
        $err_msg .= "<p class='alert alert-success' role='alert'>Príklady boli úspešne vygenerované!</p>";
    } catch (PDOException $e) {
        $err_msg .= "<p class='alert alert-danger' role='alert'>Nepodarilo sa vygenerovať príklady!</p>";
    }
} else if (!empty($_POST) && !empty($_POST["user_id"]) && empty($_POST["files"])) {
    $err_msg .= "<p class='alert alert-danger' role='alert'>Nevybrali ste žiadne súbory na generovanie!</p>";
}

// Get available files from db
try {
    $stmt = $db->prepare("SELECT * FROM files f WHERE f.active = 1");
    $stmt->execute();
    $available_files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Remove files that have already been used
    $stmt = $db->prepare("SELECT file_id FROM test WHERE student_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION["user_id"]);
    $stmt->execute();
    $used_files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($available_files as $key => $file) {
        foreach ($used_files as $used_file) {
            if ($file["id"] == $used_file["file_id"]) {
                unset($available_files[$key]);
            }
        }
    }
} catch (PDOException $e) {
    $err_msg .= "<p class='alert alert-danger' role='alert'>Nepodarilo sa načítať súbory na generovanie príkladov!</p>";
}

// Get available tasks from db
try {
    $stmt = $db->prepare("SELECT t.id, f.source, t.answer  FROM test t INNER JOIN files f ON t.file_id = f.id WHERE student_id = :user_id AND t.points_gained IS NULL");
    $stmt->bindParam(':user_id', $_SESSION["user_id"]);
    $stmt->execute();
    $student_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $err_msg .= "<p class='alert alert-danger' role='alert'>Nepodarilo sa načítať príklady na riešenie!</p>";
}

// Get task history from db
try {
    $stmt = $db->prepare("SELECT t.id, t.points_gained, f.source, f.points  FROM test t INNER JOIN files f ON t.file_id = f.id WHERE student_id = :user_id AND t.points_gained IS NOT NULL");
    $stmt->bindParam(':user_id', $_SESSION["user_id"]);
    $stmt->execute();
    $student_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $err_msg .= "<p class='alert alert-danger' role='alert'>Nepodarilo sa načítať históriu príkladov!</p>";
}
?>
<main class="mb-5">
    <section class="container w-25 mt-5">
        <?php
        echo $err_msg;
        ?>
    </section>

    <section class="container w-25 mt-5">
        <h2 class="mb-3 text-center">Generovanie príkladov</h2>
        <form action="#" method="post" class="d-flex flex-column justify-content-center">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>">
            <div class="form-check">
                <p class="mb-3">Vyber sadu príkladov z ktorej chceš generovať:</p>
                <?php
                foreach ($available_files as $file) {
                    echo '<div class="form-check d-flex flex-row justify-content-start ms-3">';
                    echo '<input class="form-check-input me-2" type="checkbox" name="files[]" value="' . $file["id"] . ':' . $file["source"] . '" id="' . $file["source"] . '">';
                    echo '<label class="form-check-label" for="' . $file["source"] . '">';
                    echo pathinfo(basename($file["source"]), PATHINFO_FILENAME);
                    echo '</label>';
                    echo '</div>';
                }
                ?>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Vygenerovať príklady</button>
        </form>
    </section>

    <section class="container w-50 mt-5">
        <h2 class="text-center">Neriešené príklady</h2>
        <table id="available" class="table table-striped mt-2" style="width:100%">
            <thead>
                <tr>
                    <th>Zdroj príkladu</th>
                    <th>Číslo príkladu</th>
                    <th>Možnosti</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                $previous = null;
                foreach ($student_tasks as $task) {
                    if ($previous) {
                        if ($task["source"] == $previous["source"]) {
                            $counter++;
                        } else {
                            $counter = 1;
                        }
                    }

                    echo '<tr>';
                    echo '<td>' . pathinfo(basename($task["source"]), PATHINFO_FILENAME) . '</td>';
                    echo '<td>' . $counter . '</td>';
                    echo '<td>';
                    echo '<a href="taskDetail.php?id=' . $task["id"] . '" class="btn btn-primary">Riešiť</a>';
                    if ($task["answer"]) {
                        echo '<button id="task-' . $task["id"] . '" class="btn btn-success ms-2" onclick="submitAnswer();">Odovzdať</button>';
                    }
                    echo '</td>';
                    echo '</tr>';
                    $previous = $task;
                }
                ?>
            </tbody>
        </table>
    </section>

    <section class="container w-50 mt-5">
        <h2 class="text-center">Odovzdané príklady</h2>
        <table id="available" class="table table-striped mt-2" style="width:100%">
            <thead>
                <tr>
                    <th>Zdroj príkladu</th>
                    <th>Číslo príkladu</th>
                    <th>Získané body</th>
                    <th>Možnosti</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                $previous = null;
                foreach ($student_history as $task) {
                    if ($previous) {
                        if ($task["source"] == $previous["source"]) {
                            $counter++;
                        } else {
                            $counter = 1;
                        }
                    }

                    echo '<tr>';
                    echo '<td>' . pathinfo(basename($task["source"]), PATHINFO_FILENAME) . '</td>';
                    echo '<td>' . $counter . '</td>';
                    echo '<td>' . $task["points_gained"] . '/' . $task["points"] . '</td>';
                    echo '<td>';
                    echo '<a href="taskSolution.php?id=' . $task["id"] . '" class="btn btn-primary">Zobraziť riešenie</a>';
                    echo '</td>';
                    echo '</tr>';
                    $previous = $task;
                }
                ?>
            </tbody>
        </table>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
    $(document).ready(function() {
        $('#available').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "Nie sú dostupné žiadne dáta",
                "info": "Zobrazených _START_ až _END_ z _TOTAL_ záznamov",
                "infoEmpty": "Zobrazených 0 až 0 z 0 záznamov",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Zobraz _MENU_ záznamov",
                "loadingRecords": "Načítavam...",
                "processing": "",
                "search": "Vyhľadať:",
                "zeroRecords": "Nenašli sa žiadne zodpovedajúce záznamy",
                "paginate": {
                    "first": "Prvá",
                    "last": "Posledná",
                    "next": "Nasledujúca",
                    "previous": "Predchádzajúca"
                },
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            }
        });
    });

    function submitAnswer() {
        const taskId = event.target.id.split("-")[1];

        // TODO: submit answer
    }
</script>