<?php
// CatalogOnline.php
session_start();

// Dacă utilizatorul este deja logat, îl trimitem direct la panoul lui
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'elev': header("Location: ElevCatalog.php"); exit();
        case 'parinte': header("Location: ParinteCatalog.php"); exit();
        case 'profesor': header("Location: ProfesorCatalog.php"); exit();
        case 'admin': header("Location: AdminDashboard.php"); exit();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Catalog Scolar - Autentificare</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Catalog Școlar Online</b></center></h1>
            <p>Aici puteți accesa catalogul elevilor și opțiunile de autentificare pentru diferite roluri.</p>

            <h2>Autentificare</h2>
            <p>Alege rolul pentru a te conecta:</p>

            <ul class="role-button-list">
                <li><button onclick="window.location.href='LogareStudent.php'">Elev</button></li>
                <li><button onclick="window.location.href='LogareParinte.php'">Părinte</button></li>
                <li><button onclick="window.location.href='LogareProfesor.php'">Profesor</button></li>
                <li><button onclick="window.location.href='LogareAdministrator.php'">Administrator</button></li>
            </ul>

            <p><a href="Home.php">Înapoi la pagina principală</a></p>
        </div>
    </body>
</html>