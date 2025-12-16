<?php
// AdminGestiuneUtilizator.php
require_once 'db_connect.php'; 
require_once 'auth_check.php';

// Ensures only the Administrator can access this page
check_authentication('admin'); 

$nume_admin = $_SESSION['user_nume'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Gestiune Utilizatori</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Gestiune Utilizatori</b></center></h1>
            <p>Panoul de control pentru administrarea conturilor (Elevi, Părinți, Profesori, Admini).</p>

            <h2>Meniul de Gestiune</h2>
            
            <ul class="admin-menu">
                <li>
                    <a href="AdminAdaugaUtilizator.php">
                        Adaugă Utilizator Nou
                    </a>
                    <p>Creează conturi noi în sistem (Elevi, Profesori, Părinți, Admini).</p>
                </li>
                <li>
                    <a href="AdminCautaEditeazaUtilizator.php">
                        Căutare și Ștergere Utilizator
                    </a>
                    <p>Caută un utilizator după nume/username și șterge conturi.</p>
                </li>
                <li>
                    <a href="AdminResetParola.php">
                        Resetare Parolă
                    </a>
                    <p>Resetează parola unui utilizator existent.</p>
                </li>
            </ul>

            <br>
            <p>
                <a href="AdminDashboard.php">
                    &lt;&lt; Înapoi la Panoul de Control
                </a>
            </p>

            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>