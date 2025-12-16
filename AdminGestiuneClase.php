<?php
// AdminGestiuneClase.php
require_once 'db_connect.php'; 
require_once 'auth_check.php';
// Asigură-te că doar Administratorul poate accesa această pagină
check_authentication('admin'); 

$clase = [];
$error = "";

try {
    // Interogare SQL: Luăm clasele și facem LEFT JOIN cu Utilizatori pentru a afla numele dirigintelui
    $sql = "SELECT 
                C.denumire, 
                C.profil, 
                U.nume, 
                U.prenume 
            FROM Clase C 
            LEFT JOIN Utilizatori U ON C.diriginte_id = U.id
            ORDER BY C.denumire";
            
    $clase = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $error = "Eroare la încărcarea claselor: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Gestiune Clase</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Gestiune Clase</b></center></h1>
            
            <?php if ($error): ?><p style="color:red; text-align:center;"><?php echo $error; ?></p><?php endif; ?>

            <p>Lista claselor înregistrate în sistem:</p>

            <div class="table-responsive-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Clasa</th>
                            <th>Profil</th>
                            <th>Diriginte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clase as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['denumire']); ?></td>
                            <td><?php echo htmlspecialchars($c['profil']); ?></td>
                            <td>
                                <?php 
                                    // Afișează numele dirigintelui sau mesajul de avertizare
                                    if ($c['nume']) {
                                        echo htmlspecialchars($c['nume'] . ' ' . $c['prenume']); 
                                    } else {
                                        echo '<span style="color:red;">Fără Diriginte</span>';
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p><a href="AdminDashboard.php">Înapoi la Panoul de Control</a></p>
            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>