<header class="p-3 text-bg-dark">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="index.php" class="nav-link px-2 text-white">Domov</a></li>
                <li><a href="studentInstructions.php" class="nav-link px-2 text-white">Návod</a></li>
            </ul>

            <div class="text-end">
                <p class="d-inline px-2">Ahoj <?php echo $_SESSION["first_name"] . " " . $_SESSION["surname"]; ?>!</p>
                <a href="authentification/logout.php" class="btn btn-warning">Odhlásiť sa</a>
            </div>
        </div>
    </div>
</header>