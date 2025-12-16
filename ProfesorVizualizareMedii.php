<?php
// ProfesorVizualizareMedii.php
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('profesor');

if (!isset($_SESSION['ctx_clasa_id'])) { header("Location: ProfesorCatalog.php"); exit(); }

$clasa_id = $_SESSION['ctx_clasa_id'];
$materie_id = $_SESSION['ctx_materie_id'];

// Interogare complexă: Luăm elevii și calculăm media/absențele
$sql = "SELECT 
            U.nume, U.prenume,
            (SELECT GROUP_CONCAT(valoare SEPARATOR ', ') FROM Note WHERE elev_utilizator_id = U.id AND materie_id = :mid) as note,
            (SELECT ROUND(AVG(valoare), 2) FROM Note WHERE elev_utilizator_id = U.id AND materie_id = :mid) as medie,
            (SELECT COUNT(*) FROM Absente WHERE elev_utilizator_id = U.id AND materie_id = :mid) as absente
        FROM Utilizatori U
        WHERE U.clasa_id = :cid AND U.rol = 'elev'
        ORDER BY U.nume";

$stmt = $pdo->prepare($sql);
$stmt->execute([':cid' => $clasa_id, ':mid' => $materie_id]);
$catalog = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Catalog Clasă</title><link rel="stylesheet" href="Style.css"></head>
<body>
    <div class="main-container">
        <h1>Situație: <?php echo $_SESSION['ctx_clasa_nume']; ?></h1>
        <div class="table-responsive-wrapper">
            <table>
                <thead><tr><th>Elev</th><th>Note</th><th>Medie</th><th>Absențe</th></tr></thead>
                <tbody>
                    <?php foreach ($catalog as $rand): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rand['nume'] . ' ' . $rand['prenume']); ?></td>
                        <td><?php echo htmlspecialchars($rand['note'] ?? '-'); ?></td>
                        <td style="font-weight:bold; color:<?php echo ($rand['medie']<5 && $rand['medie']>0)?'red':'black'; ?>">
                            <?php echo htmlspecialchars($rand['medie'] ?? '-'); ?>
                        </td>
                        <td><?php echo $rand['absente']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p><a href="ProfesorCatalog.php">Înapoi</a></p>
    </div>
</body>
</html>