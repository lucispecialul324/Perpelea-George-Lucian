<?php
// db_connect.php

define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');      
define('DB_NAME', 'proiect_tw');

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    // Setăm modul de eroare să arunce excepții
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Setăm modul implicit de fetch ca array asociativ
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Eroare critică de conexiune: " . $e->getMessage());
}
?>