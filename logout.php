<?php
// logout.php
session_start(); // Pornim sesiunea ca să știm pe cine dăm afară

// 1. Golim toate variabilele de sesiune
$_SESSION = array();

// 2. Ștergem cookie-ul de sesiune (dacă există)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Distrugem sesiunea complet
session_destroy();

// 4. Trimitem utilizatorul înapoi la pagina de alegere a rolului
header("Location: CatalogOnline.php");
exit();
?>