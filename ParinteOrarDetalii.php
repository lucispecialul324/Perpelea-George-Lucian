<?php
// ParinteOrarDetalii.php
require_once 'db_connect.php';
require_once 'auth_check.php';

check_authentication('parinte');

$parinte_id = $_SESSION['user_id'];
$elev_id = $_GET['elev_id'] ?? 0;
$nume_elev = "";
$orar_organizat = [];
$ore_posibile = [];
$zile_saptamanii = ['Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri'];
$eroare = "";

// 1. Validăm că acest copil aparține părintelui și aflăm clasa
$stmt = $pdo->prepare("
    SELECT U.nume, U.prenume, U.clasa_id 
    FROM Utilizatori U
    JOIN AsociereParinteElev APE ON U.id = APE.elev_id 
    WHERE APE.parinte_id = :pid AND U.id = :eid
");
$stmt->execute([':pid' => $parinte_id, ':eid' => $elev_id]);
$elev = $stmt->fetch();

if ($elev) {
    $nume_elev = $elev['nume'] . " " . $elev['prenume'];
    $clasa_id = $elev['clasa_id'];

    if ($clasa_id) {
        // 2. Extragem orarul
        try {
            $sql = "
                SELECT 
                    O.ziua_saptamanii, 
                    DATE_FORMAT(O.ora_inceput, '%H:%i') as ora_formatata,
                    M.denumire as materie
                FROM Orar O
                JOIN Materii M ON O.materie_id = M.materie_id
                WHERE O.clasa_id = :clasa_id
                ORDER BY O.ora_inceput ASC
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':clasa_id' => $clasa_id]);
            $rezultate = $stmt->fetchAll();

            foreach ($rezultate as $rand) {
                $ora = $rand['ora_formatata'];
                $zi = $rand['ziua_saptamanii'];
                $orar_organizat[$ora][$zi] = $rand['materie'];
                
                if (!in_array($ora, $ore_posibile)) {
                    $ore_posibile[] = $ora;
                }
            }
            sort($ore_posibile);

        } catch (PDOException $e) {
            $eroare = "Eroare la încărcarea orarului: " . $e->getMessage();
        }
    } else {
        $eroare = "Elevul nu este alocat niciunei clase.";
    }
} else {
    $eroare = "Acces interzis sau elev invalid.";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Părinte - Orar Elev</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Orar: <?php echo htmlspecialchars($nume_elev); ?></b></center></h1>

            <?php if (!empty($eroare)): ?>
                <p style="color:red; text-align:center;"><?php echo $eroare; ?></p>
            <?php elseif (empty($ore_posibile)): ?>
                <p style="text-align:center;">Orarul nu a fost încă definit pentru clasa acestui elev.</p>
            <?php else: ?>
                <div class="table-responsive-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 10%;">Ora</th>
                                <?php foreach ($zile_saptamanii as $zi): ?>
                                    <th style="width: 18%;"><?php echo $zi; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ore_posibile as $ora): ?>
                            <tr>
                                <td style="font-weight:bold; background-color:#f0f0f0;"><?php echo $ora; ?></td>
                                <?php foreach ($zile_saptamanii as $zi): ?>
                                    <td>
                                        <?php echo htmlspecialchars($orar_organizat[$ora][$zi] ?? '-'); ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <p><a href="ParinteCatalog.php">Înapoi la lista copiilor</a></p>
            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>