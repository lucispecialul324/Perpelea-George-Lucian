<?php
// ProfesorDetaliiElev.php
//session_start();
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('profesor');

$elev_id = filter_input(INPUT_GET, 'elev_id', FILTER_VALIDATE_INT);
$materie_id = $_SESSION['ctx_materie_id']; // Luăm materia din contextul activat

if (!$elev_id || !$materie_id) {
    header("Location: ProfesorRapoarteClasa.php");
    exit;
}

try {
    // 1. Informații Elev
    $stmt_elev = $pdo->prepare("SELECT nume, prenume FROM Utilizatori WHERE id = ?");
    $stmt_elev->execute([$elev_id]);
    $elev = $stmt_elev->fetch();

    // 2. Notele elevului la materia curentă
    $stmt_note = $pdo->prepare("SELECT valoare, data_nota FROM Note WHERE elev_utilizator_id = ? AND materie_id = ? ORDER BY data_nota DESC");
    $stmt_note->execute([$elev_id, $materie_id]);
    $note = $stmt_note->fetchAll();

    // 3. Absențele elevului la materia curentă
    $stmt_abs = $pdo->prepare("SELECT data_absenta, status FROM Absente WHERE elev_utilizator_id = ? AND materie_id = ? ORDER BY data_absenta DESC");
    $stmt_abs->execute([$elev_id, $materie_id]);
    $absente = $stmt_abs->fetchAll();

} catch (Exception $e) { die("Eroare: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalii Elev - <?= htmlspecialchars($elev['nume']) ?></title>
    <link rel="stylesheet" href="Style.css">
    <style>
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .section-box { background: white; padding: 15px; border-radius: 8px; border: 1px solid #ddd; }
        .grade { font-size: 1.2em; font-weight: bold; color: #28a745; }
        .abs-status { font-style: italic; color: #dc3545; }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Detalii Elev: <?= htmlspecialchars($elev['nume'] . " " . $elev['prenume']) ?></h1>
        <p>Materia: <strong><?= $_SESSION['ctx_materie_nume'] ?></strong></p>
        <a href="ProfesorRapoarteClasa.php">⬅ Înapoi la Raport</a>

        <div class="details-grid">
            <div class="section-box">
                <h3>Istoric Note</h3>
                <ul>
                    <?php if (empty($note)): ?> <li>Fără note înregistrate.</li> <?php endif; ?>
                    <?php foreach($note as $n): ?>
                        <li><span class="grade"><?= $n['valoare'] ?></span> - adăugată la <?= date('d.m.Y', strtotime($n['data_nota'])) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="section-box">
                <h3>Istoric Absențe</h3>
                <ul>
                    <?php if (empty($absente)): ?> <li>Fără absențe.</li> <?php endif; ?>
                    <?php foreach($absente as $a): ?>
                        <li><?= date('d.m.Y', strtotime($a['data_absenta'])) ?> - <span class="abs-status"><?= $a['status'] ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>