<?php
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('elev');

$nume_elev = $_SESSION['user_nume'];
$clasa_id = $_SESSION['clasa_id'];
$nume_clasa = "Neralocat";

if ($clasa_id) {
    try {
        $stmt = $pdo->prepare("SELECT denumire FROM Clase WHERE clasa_id = :id");
        $stmt->execute([':id' => $clasa_id]);
        $rez = $stmt->fetch();
        if ($rez) $nume_clasa = $rez['denumire'];
    } catch (Exception $e) { $nume_clasa = "Eroare"; }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Elev - Interfață</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <div class="notification-icon">
                <a href="notificari.php" title="Notificari">&#128276;</a>
            </div>
            
            <h1><center><b>Salut, <?php echo htmlspecialchars($nume_elev); ?>!</b></center></h1>
            <h2 style="text-align:center; color:#1a5c96;">Ești în clasa: <?php echo htmlspecialchars($nume_clasa); ?></h2>
            
            <p>Bun venit! Aici vei putea vedea informațiile tale școlare:</p>
            
            <h2>Vizualizare Informații</h2>
            <ul>
                <li><a href="afiseaza_note_si_medii.php">Note și Medii</a></li>
                <li><a href="ElevAbsenteDetalii.php">Absențe</a></li>
                <li><a href="ElevOrarDetalii.php">Orar</a></li>
            </ul>

            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>