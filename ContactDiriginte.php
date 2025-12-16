<?php
// ContactDiriginte.php
require_once 'db_connect.php';
require_once 'auth_check.php';

check_authentication('parinte'); // Doar părinții

$elev_id = $_GET['elev_id'] ?? 0;
$parinte_id = $_SESSION['user_id'];
$mesaj_status = '';
$diriginte_info = null;

// 1. Căutăm dirigintele copilului selectat
try {
    $sql = "
        SELECT 
            D.nume AS nume_diriginte, 
            D.prenume AS prenume_diriginte, 
            D.email AS email_diriginte,
            C.denumire AS nume_clasa,
            E.nume AS nume_elev,
            E.prenume AS prenume_elev
        FROM Utilizatori E
        JOIN Clase C ON E.clasa_id = C.clasa_id
        JOIN Utilizatori D ON C.diriginte_id = D.id
        JOIN AsociereParinteElev APE ON E.id = APE.elev_id
        WHERE E.id = :elev_id AND APE.parinte_id = :parinte_id
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':elev_id' => $elev_id, ':parinte_id' => $parinte_id]);
    $diriginte_info = $stmt->fetch();

} catch (PDOException $e) {
    $mesaj_status = "Eroare BD: " . $e->getMessage();
}

// 2. Procesare Formular (Simulare Trimitere)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $diriginte_info) {
    $subiect = trim($_POST['subiect']);
    $mesaj_text = trim($_POST['mesaj']);
    
    if ($subiect && $mesaj_text) {
        // AICI AI PUTEA: Salva în baza de date sau folosi mail()
        // Pentru acum, simulăm succesul:
        $mesaj_status = "<span style='color:green;'>Mesajul a fost trimis cu succes către Prof. " . htmlspecialchars($diriginte_info['nume_diriginte']) . "!</span>";
    } else {
        $mesaj_status = "<span style='color:red;'>Te rog completează toate câmpurile.</span>";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact Diriginte</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Contact Diriginte</b></center></h1>

            <?php if (!$diriginte_info): ?>
                <p style="color:red; text-align:center;">Nu am putut găsi informațiile despre diriginte. Verifică dacă elevul este alocat unei clase cu diriginte.</p>
            <?php else: ?>
                
                <div style="background:#f4f4f4; padding:15px; border-radius:8px; margin-bottom:20px;">
                    <p><strong>Destinatar:</strong> Prof. <?php echo htmlspecialchars($diriginte_info['nume_diriginte'] . ' ' . $diriginte_info['prenume_diriginte']); ?> (Diriginte Clasa <?php echo htmlspecialchars($diriginte_info['nume_clasa']); ?>)</p>
                    <p><strong>Referitor la elevul:</strong> <?php echo htmlspecialchars($diriginte_info['nume_elev'] . ' ' . $diriginte_info['prenume_elev']); ?></p>
                    <p><strong>Email Oficial:</strong> <a href="mailto:<?php echo htmlspecialchars($diriginte_info['email_diriginte']); ?>"><?php echo htmlspecialchars($diriginte_info['email_diriginte']); ?></a></p>
                </div>

                <?php if ($mesaj_status) echo "<p style='text-align:center; font-weight:bold;'>$mesaj_status</p>"; ?>

                <form method="POST" action="">
                    <fieldset>
                        <legend>Trimite un mesaj rapid</legend>
                        
                        <label for="subiect">Subiect:</label><br>
                        <input type="text" id="subiect" name="subiect" placeholder="Ex: Motivare absență..." required><br><br>
                        
                        <label for="mesaj">Mesajul dumneavoastră:</label><br>
                        <textarea id="mesaj" name="mesaj" rows="6" required placeholder="Scrieți mesajul aici..."></textarea><br><br>
                        
                        <input type="submit" value="Trimite Mesajul">
                    </fieldset>
                </form>
            <?php endif; ?>

            <p><a href="ParinteCatalog.php">Înapoi la Situația Copilului</a></p>
            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>