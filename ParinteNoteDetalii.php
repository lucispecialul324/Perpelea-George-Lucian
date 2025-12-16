<?php
// ParinteNoteDetalii.php
session_start();
require_once "db_connect.php"; 
// Asigură-te că ai o verificare de autentificare și rol 'parinte' aici.

$parinte_id = $_SESSION['user_id'] ?? 0;
$elev_id = filter_input(INPUT_GET, 'elev_id', FILTER_VALIDATE_INT);

if (!$elev_id) {
    die("Eroare: ID-ul elevului este necesar.");
}

$elev_nume = '';
$elev_clasa = '';
$note_pe_materii = [];
$eroare = '';

try {
    // 1. VERIFICARE PERMISIUNE ȘI PRELUARE DATE ELEV
    // Folosim tabela ta: AsociereParinteElev
    $stmt_permisiune = $pdo->prepare("
        SELECT 
            E.nume,
            E.prenume,
            C.denumire AS clasa_denumire
        FROM AsociereParinteElev PE 
        JOIN Utilizatori E ON PE.elev_id = E.id
        LEFT JOIN Clase C ON E.clasa_id = C.clasa_id
        WHERE PE.parinte_id = :parinte_id AND PE.elev_id = :elev_id
    ");
    $stmt_permisiune->execute([':parinte_id' => $parinte_id, ':elev_id' => $elev_id]);
    $date_elev = $stmt_permisiune->fetch();

    if (!$date_elev) {
        throw new Exception("Acces neautorizat. Nu sunteți asociat cu acest elev.");
    }
    
    $elev_nume = $date_elev['nume'] . ' ' . $date_elev['prenume'];
    $elev_clasa = $date_elev['clasa_denumire'] ?? 'Nespecificată';

    // 2. PRELUARE NOTE (Logica din codul elevului)
    $sql_note = "
        SELECT 
            M.denumire AS materie, 
            N.valoare, 
            N.data_nota,
            CASE 
                WHEN MONTH(N.data_nota) IN (9, 10, 11, 12, 1) THEN 1 
                ELSE 2 
            END AS semestru
        FROM Note N
        JOIN Materii M ON N.materie_id = M.materie_id
        WHERE N.elev_utilizator_id = :elev_id
        ORDER BY M.denumire, N.data_nota
    ";
    $stmt_note = $pdo->prepare($sql_note);
    $stmt_note->execute([':elev_id' => $elev_id]);
    $note_raw = $stmt_note->fetchAll(PDO::FETCH_ASSOC);

    // Gruparea notelor pe materii și semestre
    foreach ($note_raw as $nota) {
        $materie = $nota['materie'];
        $semestru = $nota['semestru'];
        
        if (!isset($note_pe_materii[$materie])) {
            $note_pe_materii[$materie] = ['S1' => [], 'S2' => []];
        }
        $note_pe_materii[$materie]['S'.$semestru][] = $nota['valoare'];
    }

} catch (Exception $e) {
    $eroare = $e->getMessage();
}

// Funcție de utilitate pentru medie
function calculeaza_medie($note) {
    if (empty($note)) return '-';
    return number_format(array_sum($note) / count($note), 2);
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Note Elev: <?= htmlspecialchars($elev_nume) ?></title>
    <link rel="stylesheet" href="Style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fa; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #28a745; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .header-info { background-color: #f0fff0; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #d4edda; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #28a745; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .note-list { font-weight: bold; }
        .medie { background-color: #e9ecef; font-weight: bold; text-align: center; }
        .medie-anuala { background-color:#d4edda; font-weight: bold; text-align: center; }
        .error-msg { color: #dc3545; font-weight: bold; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <a href="ParinteCatalog.php" class="back-link">⬅ Înapoi la Copii</a>
    
    <h1>Note Elev: <?= htmlspecialchars($elev_nume) ?></h1>

    <?php if ($eroare): ?>
        <p class="error-msg"><?= htmlspecialchars($eroare) ?></p>
        <?php exit; ?>
    <?php endif; ?>

    <div class="header-info">
        Clasa: **<?= htmlspecialchars($elev_clasa) ?>**
    </div>

    <?php if (empty($note_pe_materii)): ?>
        <p style="text-align:center;">Elevul nu are note înregistrate.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Materie</th>
                    <th>Note Semestrul 1</th>
                    <th>Medie S1</th>
                    <th>Note Semestrul 2</th>
                    <th>Medie S2</th>
                    <th>Media Anuală</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($note_pe_materii as $materie => $semestre): 
                    $medie_s1 = calculeaza_medie($semestre['S1']);
                    $medie_s2 = calculeaza_medie($semestre['S2']);
                    
                    $media_anuala = '-';
                    if ($medie_s1 != '-' && $medie_s2 != '-') {
                        $media_anuala = number_format(($medie_s1 + $medie_s2) / 2, 2);
                    } elseif ($medie_s1 != '-' && empty($semestre['S2'])) {
                         $media_anuala = $medie_s1;
                    } elseif ($medie_s2 != '-' && empty($semestre['S1'])) {
                        $media_anuala = $medie_s2;
                    }
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($materie) ?></strong></td>
                    <td class="note-list"><?= empty($semestre['S1']) ? '-' : implode(', ', $semestre['S1']) ?></td>
                    <td class="medie"><?= $medie_s1 ?></td>
                    <td class="note-list"><?= empty($semestre['S2']) ? '-' : implode(', ', $semestre['S2']) ?></td>
                    <td class="medie"><?= $medie_s2 ?></td>
                    <td class="medie-anuala"><?= $media_anuala ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p style="margin-top: 20px;"><a href="ParinteAbsenteDetalii.php?elev_id=<?= $elev_id ?>">Vezi Absențe</a></p>
</div>

</body>
</html>