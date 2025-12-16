<?php
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('parinte');

$parinte_id = $_SESSION['user_id'];
$nume_parinte = $_SESSION['user_nume'];

try {
    $sql = "SELECT U.id, U.nume, U.prenume, C.denumire AS clasa 
            FROM Utilizatori U
            JOIN AsociereParinteElev APE ON U.id = APE.elev_id
            LEFT JOIN Clase C ON U.clasa_id = C.clasa_id
            WHERE APE.parinte_id = :pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $parinte_id]);
    $copii = $stmt->fetchAll();
} catch (Exception $e) { $copii = []; }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Părinte - Interfață</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Panou Părinte: <?php echo htmlspecialchars($nume_parinte); ?></b></center></h1>
            <p>Selectați copilul pentru a vedea detaliile:</p>
            
            <?php if (count($copii) > 0): ?>
                <?php foreach ($copii as $copil): ?>
                    <div style="border:1px solid #ccc; padding:20px; margin-bottom:20px; border-radius:10px; background-color:#f9f9f9;">
                        <h2 style="color:#1a5c96; margin-top:0;">
                            Elev: <?php echo htmlspecialchars($copil['nume'] . " " . $copil['prenume']); ?>
                        </h2>
                        <p><strong>Clasa:</strong> <?php echo htmlspecialchars($copil['clasa'] ?? 'Neralocat'); ?></p>
                        <ul>
                            <li><a href="ParinteNoteDetalii.php?elev_id=<?php echo $copil['id']; ?>">Vezi Note</a></li>
                            <li><a href="ParinteAbsenteDetalii.php?elev_id=<?php echo $copil['id']; ?>">Vezi Absențe</a></li>
                            <li><a href="ParinteOrarDetalii.php?elev_id=<?php echo $copil['id']; ?>">Vezi Orar</a></li>
                            <li><a href="ContactDiriginte.php?elev_id=<?php echo $copil['id']; ?>">Contact Diriginte</a></li>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:red; font-weight:bold;">Nu aveți niciun elev asociat contului dumneavoastră.</p>
            <?php endif; ?>

            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>