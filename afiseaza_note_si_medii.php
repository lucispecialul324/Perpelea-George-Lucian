<?php
// afiseaza_note_si_medii.php
session_start();
require_once "db_connect.php";

// 1. Verificare autentificare
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$elev_id = $_SESSION['user_id'];

// Inițializare variabile
$nume_elev = "Elev";
$nume_clasa = "Fără clasă";
$catalog = [];

try {
    // PASUL 1: Preluăm Numele și Clasa
    $sql_elev = "
        SELECT u.nume, u.prenume, c.denumire AS nume_clasa
        FROM Utilizatori u
        LEFT JOIN Clase c ON u.clasa_id = c.clasa_id
        WHERE u.id = ?
    ";
    $stmt = $pdo->prepare($sql_elev);
    $stmt->execute([$elev_id]);
    $info = $stmt->fetch();

    if ($info) {
        $nume_elev = $info['nume'] . ' ' . $info['prenume'];
        $nume_clasa = $info['nume_clasa'] ?? "Nerepartizat";
    }

    // PASUL 2: Preluăm Notele
    $sql_note = "
        SELECT m.denumire AS materie, n.valoare, n.data_nota
        FROM Note n
        JOIN Materii m ON n.materie_id = m.materie_id
        WHERE n.elev_utilizator_id = ?
        ORDER BY m.denumire ASC, n.data_nota ASC
    ";
    $stmt = $pdo->prepare($sql_note);
    $stmt->execute([$elev_id]);
    $toate_notele = $stmt->fetchAll();

    // PASUL 3: Procesare Note
    foreach ($toate_notele as $rand) {
        $materie = $rand['materie'];
        $nota = (float)$rand['valoare'];
        $data = $rand['data_nota'];

        // Deducem semestrul
        $luna = (int)date('m', strtotime($data));
        $semestru = ($luna >= 9 || $luna == 1) ? 'S1' : 'S2';

        if (!isset($catalog[$materie])) {
            $catalog[$materie] = ['S1' => [], 'S2' => []];
        }
        $catalog[$materie][$semestru][] = $nota;
    }

} catch (PDOException $e) {
    die("Eroare la baza de date: " . $e->getMessage());
}

function calculeaza_medie($note) {
    if (empty($note)) return '-';
    $suma = array_sum($note);
    $count = count($note);
    return round($suma / $count, 2);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Catalog Elev</title>
    <link rel="stylesheet" type="text/css" href="Style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fa; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-info { background: #e9ecef; padding: 15px; margin-bottom: 20px; border-radius: 5px; border-left: 5px solid #007bff; }
        
        /* STIL BUTON ÎNAPOI */
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #6c757d; /* Gri */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-back:hover {
            background-color: #5a6268; /* Gri mai închis la hover */
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .medie-col { font-weight: bold; background-color: #e3f2fd; }
        .anuala-col { font-weight: bold; background-color: #d4edda; }
        .no-data { text-align: center; padding: 20px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    </style>
</head>
<body>

<div class="container">
    <a href="ElevCatalog.php" class="btn-back">⬅ Înapoi la Panou</a>

    <h1>Situație Școlară</h1>

    <div class="header-info">
        <strong>Elev:</strong> <?= htmlspecialchars($nume_elev) ?> <br>
        <strong>Clasa:</strong> <?= htmlspecialchars($nume_clasa) ?>
    </div>

    <?php if (empty($catalog)): ?>
        <div class="no-data">
            Nu ai nicio notă înregistrată momentan.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Materie</th>
                    <th colspan="2">Semestrul 1</th>
                    <th colspan="2">Semestrul 2</th>
                    <th rowspan="2">Media Anuală</th>
                </tr>
                <tr>
                    <th>Note</th>
                    <th>Media</th>
                    <th>Note</th>
                    <th>Media</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($catalog as $materie => $semestre): 
                    $m1 = calculeaza_medie($semestre['S1']);
                    $m2 = calculeaza_medie($semestre['S2']);
                    
                    $anuala = '-';
                    if ($m1 !== '-' && $m2 !== '-') {
                        $anuala = round(($m1 + $m2) / 2, 2);
                    }
                ?>
                <tr>
                    <td style="text-align: left; font-weight: bold;"><?= htmlspecialchars($materie) ?></td>
                    <td><?= !empty($semestre['S1']) ? implode(', ', $semestre['S1']) : '-' ?></td>
                    <td class="medie-col"><?= $m1 ?></td>
                    <td><?= !empty($semestre['S2']) ? implode(', ', $semestre['S2']) : '-' ?></td>
                    <td class="medie-col"><?= $m2 ?></td>
                    <td class="anuala-col"><?= $anuala ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="font-size: 0.85em; color: #666; margin-top: 10px;">
            * Mediile sunt calculate aritmetic. Semestrul este determinat automat pe baza datei notei.
        </p>
    <?php endif; ?>
</div>

</body>
</html>