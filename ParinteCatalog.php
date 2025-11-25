<?php
// ParinteCatalog.php
require_once 'db_connect.php';
require_once 'auth_check.php';

check_authentication('parinte'); // Doar părinții au voie aici

$parinte_id = $_SESSION['user_id'];
$nume_parinte = $_SESSION['user_nume'];

// Interogare complexă: Găsim copiii asociați acestui părinte și clasa lor
$sql = "SELECT U.id, U.nume, U.prenume, C.denumire AS clasa 
        FROM Utilizatori U
        JOIN AsociereParinteElev APE ON U.id = APE.elev_id
        LEFT JOIN Clase C ON U.clasa_id = C.clasa_id
        WHERE APE.parinte_id = :pid";

$stmt = $pdo->prepare($sql);
$stmt->execute([':pid' => $parinte_id]);
$copii = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Părinte - Interfață</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Părinte: <?php echo htmlspecialchars($nume_parinte); ?></b></center></h1>
            <p>Mai jos sunt copiii asociați contului dumneavoastră:</p>
            
            <?php if (count($copii) > 0): ?>
                <?php foreach ($copii as $copil): ?>
                    <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px; border-radius:8px; background:#f9f9f9;">
                        <h3>Elev: <?php echo htmlspecialchars($copil['nume'] . " " . $copil['prenume']); ?></h3>
                        <p><strong>Clasa:</strong> <?php echo htmlspecialchars($copil['clasa'] ?? 'Neralocată'); ?></p>
                        
                        <ul>
                            <li><a href="ParinteNoteDetalii.php?elev_id=<?php echo $copil['id']; ?>">Vezi Note</a></li>
                            <li><a href="ParinteAbsenteDetalii.php?elev_id=<?php echo $copil['id']; ?>">Vezi Absențe</a></li>
                            <li><a href="ContactDiriginte.php?elev_id=<?php echo $copil['id']; ?>">Contact Diriginte</a></li>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:red;">Nu aveți niciun elev asociat. Contactați administratorul.</p>
            <?php endif; ?>

            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>