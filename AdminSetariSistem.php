<?php
// AdminSetariSistem.php
require_once 'db_connect.php'; 
require_once 'auth_check.php';
// Asigură-te că doar Administratorul poate accesa această pagină
check_authentication('admin'); 

$materii = [];
$error = "";

try {
    // Extrage toate materiile definite în sistem
    $sql = "SELECT materie_id, denumire, ore_saptamanale 
            FROM Materii 
            ORDER BY denumire";
            
    $materii = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $error = "Eroare la încărcarea materiilor: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Setări Sistem</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Setări Sistem & Structură</b></center></h1>
            
            <?php if ($error): ?><p style="color:red; text-align:center;"><?php echo $error; ?></p><?php endif; ?>

            <p>Gestionați aici opțiunile generale ale catalogului (materii, structura anului școlar, etc.).</p>

            <h2>Materii Definite</h2>
            
            <?php if (empty($materii)): ?>
                <p>Nu există materii înregistrate în baza de date.</p>
            <?php else: ?>
                <div class="table-responsive-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Denumire</th>
                                <th>Ore Săptămânale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materii as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['materie_id']); ?></td>
                                <td><?php echo htmlspecialchars($m['denumire']); ?></td>
                                <td><?php echo htmlspecialchars($m['ore_saptamanale']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <h2>Structura Anului Școlar</h2>
            <ul>
                <li><a href="set_an_scolar.php">Setează Anul Școlar Curent (Simulat)</a></li>
                <li><a href="gestiune_semestre.php">Gestiune Semestre (Simulat)</a></li>
            </ul>

            <p><a href="AdminDashboard.php">Înapoi la Panoul de Control</a></p>
            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>