<?php
// notificari.php
session_start();
require_once "db_connect.php";

// 1. VERIFICARE ACCES
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_nume = $_SESSION['user_nume'] ?? "Utilizator";
$user_role = $_SESSION['user_role'] ?? "elev";
$mesaj_actiune = '';
$notificari = [];

try {
    // ----------------------------------------------------------------------
    // ACȚIUNE 1: MARCAREA NOTIFICĂRILOR CA CITITE
    // Acesta este un pas important de UX: când utilizatorul deschide pagina, notificările devin citite.
    // Presupunem că tabela 'Notificari' există.
    // ----------------------------------------------------------------------
    $stmt_update = $pdo->prepare("
        UPDATE Notificari 
        SET citita = 1 
        WHERE utilizator_id = ? AND citita = 0
    ");
    $stmt_update->execute([$user_id]);


    // ----------------------------------------------------------------------
    // ACȚIUNE 2: PRELUAREA NOTIFICĂRILOR SPECIFICE USER-ULUI CONECTAT
    // Dacă tabela nu conține înregistrări pentru acest user, $notificari va fi un array gol.
    // ----------------------------------------------------------------------
    $sql = "
        SELECT id, mesaj, data_creare, citita 
        FROM Notificari 
        WHERE utilizator_id = ? 
        ORDER BY data_creare DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $notificari = $stmt->fetchAll();

} catch (PDOException $e) {
    // Eroare la DB (cel mai probabil pentru că tabela 'Notificari' nu a fost încă creată)
    error_log("SQL Error Notificari: " . $e->getMessage());
    $mesaj_actiune = "Eroare la baza de date. Verificați dacă tabela 'Notificari' există și este corectă. (Detalii logate)";
    $notificari = []; // Asigurăm că array-ul este gol pentru a afișa mesajul de eroare
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Notificări - <?= htmlspecialchars($user_nume) ?></title>
    <link rel="stylesheet" href="Style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fa; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #007bff; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        
        .notification-list { list-style: none; padding: 0; }
        .notification-item {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }
        
        .notification-item.unread {
            background-color: #e6f7ff;
            border-left: 5px solid #007bff;
            font-weight: 500;
        }
        .notification-item.read {
            background-color: #f9f9f9;
            color: #777;
        }

        .notification-date {
            font-size: 0.85em;
            color: #999;
            white-space: nowrap;
        }
        
        .back-link { 
            display: inline-block; 
            margin-bottom: 20px; 
            padding: 10px 15px;
            background-color: #6c757d; 
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .error-msg { color: #dc3545; font-weight: bold; margin-bottom: 15px; border: 1px solid #dc3545; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <a href="ElevCatalog.php" class="back-link">⬅ Înapoi la Panou</a>

    <h1>Notificări (<?= htmlspecialchars($user_nume) ?>)</h1>

    <?php if ($mesaj_actiune): ?>
        <p class="error-msg"><?= $mesaj_actiune ?></p>
    <?php endif; ?>

    <?php if (empty($notificari)): ?>
        <p style="text-align: center; padding: 30px; color: #777;">
            Nu ai notificări noi.
        </p>
    <?php else: ?>
        <ul class="notification-list">
            <?php foreach ($notificari as $n): 
                $status_class = $n['citita'] ? 'read' : 'unread';
                $data_formatata = date("d M Y, H:i", strtotime($n['data_creare']));
            ?>
            <li class="notification-item <?= $status_class ?>">
                <span><?= htmlspecialchars($n['mesaj']) ?></span>
                <span class="notification-date"><?= $data_formatata ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

</body>
</html>