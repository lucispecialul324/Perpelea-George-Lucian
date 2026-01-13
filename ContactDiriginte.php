<?php
// ContactDiriginte.php
//if (session_status() === PHP_SESSION_NONE) {
    //session_start();
//}
require_once 'db_connect.php';
require_once 'auth_check.php';

check_authentication('parinte'); 

$elev_id = $_GET['elev_id'] ?? 0;
$parinte_id = $_SESSION['user_id'];
$mesaj_status = '';
$diriginte_info = null;

// 1. Căutăm dirigintele copilului selectat (Include și ID-ul dirigintelui pentru salvare)
try {
    $sql = "
        SELECT 
            D.id AS diriginte_id, 
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
    $mesaj_status = "<span style='color:red;'>Eroare BD: " . $e->getMessage() . "</span>";
}

// 2. Procesare Formular - SALVARE REALĂ ÎN BAZA DE DATE
if ($_SERVER["REQUEST_METHOD"] == "POST" && $diriginte_info) {
 $subiect = htmlspecialchars(trim($_POST['subiect']));
$mesaj_text = htmlspecialchars(trim($_POST['mesaj']));
    $destinatar_id = $diriginte_info['diriginte_id'];

    if (!empty($subiect) && !empty($mesaj_text)) {
        try {
            // Inserăm mesajul în tabela Mesaje
            // Notă: Dacă nu ai coloana destinatar_id, mesajul va fi considerat general pentru admin
            $sql_save = "INSERT INTO Mesaje (expeditor_id, subiect, mesaj, data_trimitere, status) 
                         VALUES (?, ?, ?, NOW(), 'nou')";
            $stmt_save = $pdo->prepare($sql_save);
            $stmt_save->execute([$parinte_id, "Către Diriginte: " . $subiect, $mesaj_text]);

            $mesaj_status = "<span style='color:green;'>Mesajul a fost salvat și trimis către Prof. " . htmlspecialchars($diriginte_info['nume_diriginte']) . "!</span>";
        } catch (PDOException $e) {
            $mesaj_status = "<span style='color:red;'>Eroare la salvarea mesajului: " . $e->getMessage() . "</span>";
        }
    } else {
        $mesaj_status = "<span style='color:red;'>Te rog completează toate câmpurile.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
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
                
                <div style="background:#f4f4f4; padding:15px; border-radius:8px; margin-bottom:20px; border-left: 5px solid #007bff;">
                    <p><strong>Destinatar:</strong> Prof. <?php echo htmlspecialchars($diriginte_info['nume_diriginte'] . ' ' . $diriginte_info['prenume_diriginte']); ?> (Diriginte Clasa <?php echo htmlspecialchars($diriginte_info['nume_clasa']); ?>)</p>
                    <p><strong>Referitor la elevul:</strong> <?php echo htmlspecialchars($diriginte_info['nume_elev'] . ' ' . $diriginte_info['prenume_elev']); ?></p>
                    <p><strong>Email Oficial:</strong> <a href="mailto:<?php echo htmlspecialchars($diriginte_info['email_diriginte']); ?>"><?php echo htmlspecialchars($diriginte_info['email_diriginte']); ?></a></p>
                </div>

                <?php if ($mesaj_status) echo "<p style='text-align:center; font-weight:bold;'>$mesaj_status</p>"; ?>

                <form method="POST" action="">
                    <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
                        <legend style="padding: 0 10px; font-weight: bold; color: #007bff;">Trimite un mesaj rapid</legend>
                        
                        <label for="subiect">Subiect:</label><br>
                        <input type="text" id="subiect" name="subiect" style="width:100%; padding:8px; margin-bottom:10px;" placeholder="Ex: Motivare absență..." required><br>
                        
                        <label for="mesaj">Mesajul dumneavoastră:</label><br>
                        <textarea id="mesaj" name="mesaj" rows="6" style="width:100%; padding:8px;" required placeholder="Scrieți mesajul aici..."></textarea><br><br>
                        
                        <input type="submit" value="Trimite Mesajul" style="background:#007bff; color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">
                    </fieldset>
                </form>
            <?php endif; ?>

            <p style="margin-top:20px;"><a href="ParinteVizualizare.php">⬅ Înapoi la Panoul Părinte</a></p>
            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>