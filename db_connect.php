<?php
// db_connect.php

define('DB_HOST', 'mysql');
define('DB_PORT', '3039');
define('DB_USER', 'user');
define('DB_PASS', 'password');      
define('DB_NAME', 'studenti');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    // Setăm modul de eroare să arunce excepții
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Setăm modul implicit de fetch ca array asociativ
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Eroare critică de conexiune: " . $e->getMessage());
}
?>