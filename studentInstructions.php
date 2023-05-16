<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('config.php');

if (!isset($_SESSION["user_id"])) {
  header("location: login.php");
}

if ($_SESSION["type"] != "Student") {
  header("location: index.php");
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
    <link href="https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    
    
    <style>
    .pdf-width {
        width: 100% !important;
    }
</style>

<script defer>
    function generatePDF() {
        const element = document.getElementById('content');

        // Create a clone of the content div
        const clone = element.cloneNode(true);

        // Add the CSS class to the clone for PDF generation
        clone.classList.add('pdf-width');

        const options = {
            filename: 'generated_pdf.pdf',
            margin: [10, 10, 10, 10],
            image: { type: 'jpeg', quality: 2 },
            html2canvas: { scale: 2 }, // Adjust the scale value as needed
            jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
        };

        html2pdf().from(clone).set(options).save();

        // Remove the clone from the document
        clone.remove();
    }
</script>
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
        <section class="container mt-5 mb-5 w-50" id="content">
            <h3 class="text-center">Návod na použitie - študent</h3>
            <br>
            <p>Po prihlásení na hlavnej stránke vidíme 3 hlavné časti. Časť pre generovanie príkladov, neriešené príklady a odovzdané príklady, kde môžme vidieť históriu odovzdaných príkladov</p>
            <br>
            <h2>Vygenerovanie príkladov na riešenie</h2>
  <p>
    Pre vygenerovanie príkladov postupujte nasledovne:
  </p>
  <ul>
    <li>Vyberte si zoznam latexových súborov, z ktorých chcete generovať príklady.</li>
    <li>Kliknite na tlačidlo "Vygenerovať príklady".</li>
    <li>V časti neriešené príklady sa vám zobrazia vygenerované príklady.</li>
  </ul>
  
  <h2>Riešenie príkladov</h2>
  <p>
    Pre riešenie a odovzdanie úloh postupujte nasledovne:
  </p>
  <ul>
  <li>Pri každom vygenerovanom príklade je možnosť riešiť príklad</li>
    <li>Po kliknutí tlačidlo riešiť sa vám zobrazí detail zadanej úlohy.</li>
    <li> Nachádza sa tam pole pre vloženie vášho riešenia s možnosťami ULOŽIŤ, alebo ODOVZDAŤ riešenie</li>
    <li>Pre uloženie riešenia kliknite na tlačidlo ULOŽIŤ</li>
    <li>Pre odovzdanie riešenia kliknite na tlačidlo ODOVZDAŤ</li>
  </ul>
  
            
        </section>
        <button onclick="generatePDF()"  class="btn btn-light mx-auto mt-6 d-block">Stiahnuť PDF</button>
    </main>

    <script src="https://unpkg.com/mathlive"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    


    
</body>

</html>