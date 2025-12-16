<?php
// AdminDashboard.php
require_once 'auth_check.php';
check_authentication('admin');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administrator - Interfață</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Administrator: Panou de Control</b></center></h1>
            <p>Bun venit! Aici veți gestiona întregul sistem al catalogului școlar.</p>

            <h2>Gestiune Utilizatori</h2>
            <ul>
                <li><a href="AdminAdaugaUtilizator.php">Adaugă Utilizator Nou</a> (Elevi, Profesori, Părinți)</li>
                <li><a href="AdminCautaEditeazaUtilizator.php">Caută și Șterge Utilizatori</a></li>
                <li><a href="AdminResetParola.php">Resetare Parolă Globală</a></li>
            </ul>
            
            <h2>Gestiune Structură Școlară</h2>
            <ul>
                <li><a href="AdminGestiuneClase.php">Gestiune Clase</a> (Vezi clasele și diriginții)</li>
                <li><a href="AdminAlocareProfesor.php">Alocare Profesori</a> (Leagă profesorii de clase)</li>
                <li><a href="AdminSetariSistem.php">Setări Sistem</a> (Materii, An școlar)</li>
            </ul>

            <p><a href="logout.php?logout=true" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>