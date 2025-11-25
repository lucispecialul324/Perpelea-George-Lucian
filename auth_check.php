<?php
session_start();

function check_authentication($rol_necesar = null) {
    // 1. Verificăm dacă utilizatorul e logat
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        header("Location: CatalogOnline.html"); // Trimite-l înapoi la login
        exit();
    }

    // 2. Verificăm dacă are rolul corect (ex: un elev nu poate intra la pagina de profesori)
    if ($rol_necesar !== null && $_SESSION['user_role'] !== $rol_necesar) {
        echo "Acces interzis! Nu aveți drepturile necesare.";
        exit();
    }
}
?>