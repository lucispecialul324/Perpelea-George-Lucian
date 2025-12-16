<?php
// ParinteAbsenteDetalii.php
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
$absente_elev = [];
$eroare = '';

try {
    // 1. VERIFICARE PERMISIUNE ȘI PRELUARE DATE ELEV
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

    // 2. PRELUARE ABSENȚE (Logica din codul elevului)
    $sql_absente = "
        SELECT 
            A.data_absenta,
            A.status,
            M.denumire AS materie_denumire,
            P.nume AS prof_nume,
            P.prenume AS prof_prenume
        FROM Absente A
        JOIN Materii M ON A.materie_id = M.materie_id
        LEFT JOIN Utilizatori P ON A.profesor_utilizator_id = P.id
        WHERE A.elev_utilizator_id = :elev_id
        ORDER BY A.data_absenta DESC
    ";
    
    $stmt_absente = $pdo->prepare($sql_absente);
    $stmt_absente->execute([':elev_id' => $elev_id]);
    $absente_elev = $stmt_absente->fetchAll();

} catch (Exception $e) {
    $eroare = $e->getMessage();
}

// Funcție de utilitate pentru status
function traduce_status($status) {
    switch ($status) {
        case 'motivata': return 'Motivată';
        case 'nemotivata': return 'Nemotivată';
        case 'in_asteptare': return 'În Așteptare';
        default: return ucfirst($status);
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Absențe Elev: <?= htmlspecialchars($elev_nume) ?></title>
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
        
        /* Stiluri Absențe */
        .status-nemotivata { background-color: #f8d7da; color: #721c24; font-weight: bold; }
        .status-motivata { background-color: #d4edda; color: #155724; }
        .status-in_asteptare { background-color: #fff3cd; color: #856404; }
        .error-msg { color: #dc3545; font-weight: bold; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <a href="ParinteCatalog.php" class="back-link">⬅ Înapoi la Copii</a>
    
    <h1>Absențe Elev: <?= htmlspecialchars($elev_nume) ?></h1>

    <?php if ($eroare): ?>
        <p class="error-msg"><?= htmlspecialchars($eroare) ?></p>
        <?php exit; ?>
    <?php endif; ?>

    <div class="header-info">
        Clasa: **<?= htmlspecialchars($elev_clasa) ?>**
    </div>

    <?php if (empty($absente_elev)): ?>
        <p style="text-align:center;">Elevul nu are absențe înregistrate.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data & Ora</th>
                    <th>Materie</th>
                    <th>Status</th>
                    <th>Profesor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($absente_elev as $abs): 
                    $data_formatata = date("d M Y, H:i", strtotime($abs['data_absenta']));
                    $profesor = htmlspecialchars($abs['prof_nume'] . ' ' . $abs['prof_prenume']);
                    $status_tradus = traduce_status($abs['status']);
                    $status_class = 'status-' . htmlspecialchars($abs['status']);
                ?>
                <tr>
                    <td><?= $data_formatata ?></td>
                    <td><?= htmlspecialchars($abs['materie_denumire']) ?></td>
                    <td class="<?= $status_class ?>"><?= $status_tradus ?></td>
                    <td><?= $profesor ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p style="margin-top: 20px;"><a href="ParinteNoteDetalii.php?elev_id=<?= $elev_id ?>">Vezi Note</a></p>
</div>

</body>
</html>