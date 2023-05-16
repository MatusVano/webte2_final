<?php

require_once('config.php');

$err_msg = "";

try {

    $query_students = "SELECT acc.id, acc.first_name, acc.surname, COUNT(test.question) as questions, COUNT(test.points_gained) as answers, SUM(test.points_gained) as points
                    FROM test AS test INNER JOIN account AS acc ON acc.id = test.student_id
                    GROUP BY acc.username";

    $stmt_students = $db->query($query_students);

    $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $err_msg .= "<p class='alert alert-danger' role='alert'>Nepodarilo sa načítať záznamy!</p>";
}
?>

<main class="mb-5">
    <section class="container w-25 mt-5">
        <?php
        echo $err_msg;
        ?>
    </section>

    <section class="container mt-5">
        <h2 class="text-center">Študenti</h2>
        <table id="available" class="table table-striped mt-2" style="width:100%">
            <thead>
                <tr>
                    <th>ID študenta</th>
                    <th>Meno študenta</th>
                    <th>Prizvisko študenta</th>
                    <th>Počet vygenerovaných úloh</th>
                    <th>Počet odovzdaných úloh</th>
                    <th>Počet získaných bodov</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //                echo '<a href="taskDetail.php?id=' . $task["id"] . '" class="btn btn-primary">Riešiť</a>';
                foreach ($students as $student) {
                    echo '<tr><td><a href="studentReport.php?id=' . $student['id'] . '">' . $student['id'] . '</a></td>';
                    echo '<td><a href="studentReport.php?id=' . $student['id'] . '">' . $student['first_name'] . '</a></td>';
                    echo '<td><a href="studentReport.php?id=' . $student['id'] . '">' . $student['surname'] . '</a></td>';
                    echo '<td>' . $student['questions'] . '</td>';
                    echo '<td>' . $student['answers'] . '</td>';
                    echo '<td>' . $student['points'] . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </section>
    <br>
    <button id="exportButton" class="btn btn-light mx-auto mt-6 d-block">Exportovať do CSV</button>
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
    function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll('table tr');
  
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
    
    for (var j = 0; j < cols.length; j++) {
      var cellValue = cols[j].innerText;
      var encodedValue = encodeURIComponent(cellValue);
      row.push(encodedValue);
    }
    
    csv.push(row.join(','));
    }
  
  
    var csvContent = csv.join('\n');
  
  
    var link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + '\uFEFF' + csvContent;
    link.target = '_blank';
    link.download = filename + '.csv';
    link.click();
    }


    var tableExportButton = document.getElementById('exportButton');
    tableExportButton.addEventListener('click', function() {
    exportTableToCSV('export');
    });

</script>
