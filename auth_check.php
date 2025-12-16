<?php
// auth_check.php
session_start();


function check_authentication($rol_necesar = null) {
    
    // 1. Verifică Autentificarea
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        // Dacă nu e logat, îl trimite la pagina de login.
        header("Location: CatalogOnline.php"); 
        exit();
    }

    // 2. Verifică Rolul
    if ($rol_necesar !== null && $_SESSION['user_role'] !== $rol_necesar) {
        // Dacă e logat, dar rolul e greșit
        die("Acces interzis! Nu aveți drepturile necesare pentru această secțiune.");
    }
}
?>