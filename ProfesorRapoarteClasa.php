<?php
// ProfesorRapoarteClasa.php
//if (session_status() === PHP_SESSION_NONE) {
    //session_start();
//}
require_once 'db_connect.php';
require_once 'auth_check.php';

// Verificăm dacă utilizatorul este profesor
check_authentication('profesor');

// Verificăm dacă există o clasă și o materie activate în sesiune
if (!isset($_SESSION['ctx_clasa_id']) || !isset($_SESSION['ctx_materie_id'])) {
    header("Location: ProfesorCatalog.php");
    exit;
}

$clasa_id = $_SESSION['ctx_clasa_id'];
$materie_id = $_SESSION['ctx_materie_id'];
$clasa_nume = $_SESSION['ctx_clasa_nume'];
$materie_nume = $_SESSION['ctx_materie_nume'];

try {
    /**
     * Interogare pentru raport:
     * - Luăm toți elevii din clasa selectată
     * - Calculăm media notelor lor DOAR la materia activă
     * - Numărăm absențele totale la materia activă
     */
    $sql = "SELECT 
                U.id, 
                U.nume, 
                U.prenume,
                (SELECT ROUND(AVG(valoare), 2) FROM Note WHERE elev_utilizator_id = U.id AND materie_id = :mid) as medie,
                (SELECT COUNT(*) FROM Absente WHERE elev_utilizator_id = U.id AND materie_id = :mid) as total_absente
            FROM Utilizatori U
            WHERE U.clasa_id = :cid AND U.rol = 'elev'
            ORDER BY U.nume ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['mid' => $materie_id, 'cid' => $clasa_id]);
    $raport = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Eroare la generarea raportului: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Clasă - <?= htmlspecialchars($clasa_nume) ?></title>
    <link rel="stylesheet" href="Style.css">
    <style>
        .report-header {
            background: #f4f7f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover { background-color: #f1f1f1; }
        .btn-detail {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-detail:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Raport Situație Elevi</h1>
        
        <div class="report-header">
            <p>Clasa: <strong><?= htmlspecialchars($clasa_nume) ?></strong></p>
            <p>Materia: <strong><?= htmlspecialchars($materie_nume) ?></strong></p>
        </div>

        <p><em>Sfat: Apăsați pe numele unui elev pentru a vedea istoricul complet de note și absențe.</em></p>

        <table>
            <thead>
                <tr>
                    <th>Nume Elev</th>
                    <th style="text-align:center;">Media Curentă</th>
                    <th style="text-align:center;">Total Absențe</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($raport)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">Nu există elevi în această clasă.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($raport as $r): ?>
                    <tr>
                        <td>
                            <a href="ProfesorDetaliiElev.php?elev_id=<?= $r['id'] ?>" class="btn-detail">
                                <?= htmlspecialchars($r['nume'] . " " . $r['prenume']) ?>
                            </a>
                        </td>
                        <td style="text-align:center;">
                            <strong><?= $r['medie'] ?? "-" ?></strong>
                        </td>
                        <td style="text-align:center;">
                            <?= $r['total_absente'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <a href="ProfesorCatalog.php" class="btn-link">⬅ Înapoi la Catalog</a>
        </div>
    </div>
</body>
</html>