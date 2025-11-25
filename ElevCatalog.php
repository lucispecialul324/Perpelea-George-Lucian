<?php
// ElevCatalog.php
require_once 'db_connect.php';
require_once 'auth_check.php';

// Verificăm dacă e logat și dacă e elev
check_authentication('elev');

$nume_elev = $_SESSION['user_nume'];
$clasa_id = $_SESSION['clasa_id'];
$nume_clasa = "Necunoscută";

// Extragem numele clasei din DB
if ($clasa_id) {
    $stmt = $pdo->prepare("SELECT denumire FROM Clase WHERE clasa_id = :id");
    $stmt->execute([':id' => $clasa_id]);
    $rezultat = $stmt->fetch();
    if ($rezultat) {
        $nume_clasa = $rezultat['denumire'];
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Elev - Interfață</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <div class="notification-icon">
                <a href="ElevNotificari.html" title="Notificari">&#128276;</a>
            </div>
            
            <h1><center><b>Elev: <?php echo htmlspecialchars($nume_elev); ?></b></center></h1>
            <h2 style="text-align:center; color:#4CAF50;">Clasa: <?php echo htmlspecialchars($nume_clasa); ?></h2>
            
            <p>Bun venit! Aici vei putea vedea informațiile tale:</p>
            
            <h2>Vizualizare Informații</h2>
            <ul>
                <li><a href="ElevNoteDetalii.php">Note și Medii</a></li>
                <li><a href="ElevAbsenteDetalii.html">Absențe</a></li>
                <li><a href="ElevOrarDetalii.html">Orar</a></li>
            </ul>

            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>