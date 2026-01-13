<?php
// ProfesorAdaugaAbsenta.php
//session_start();
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('profesor');

// Verificăm dacă există o clasă activată în sesiune
if (!isset($_SESSION['ctx_clasa_id'])) {
    header("Location: ProfesorCatalog.php");
    exit;
}

$clasa_id = $_SESSION['ctx_clasa_id'];
$materie_id = $_SESSION['ctx_materie_id'];
$profesor_id = $_SESSION['user_id'];
$mesaj = "";

try {
    // Luăm lista de elevi din clasa activă
    $stmt = $pdo->prepare("SELECT id, nume, prenume FROM Utilizatori WHERE clasa_id = ? AND rol = 'elev' ORDER BY nume");
    $stmt->execute([$clasa_id]);
    $elevi = $stmt->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elev_id'])) {
        $stmt_ins = $pdo->prepare("INSERT INTO Absente (elev_utilizator_id, profesor_utilizator_id, materie_id, data_absenta, status) VALUES (?, ?, ?, NOW(), ?)");
        $stmt_ins->execute([$_POST['elev_id'], $profesor_id, $materie_id, $_POST['status']]);
        $mesaj = "Absență înregistrată cu succes!";
    }
} catch (Exception $e) { $mesaj = "Eroare: " . $e->getMessage(); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Adaugă Absență</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="main-container">
        <h1>Absență: <?php echo $_SESSION['ctx_clasa_nume']; ?></h1>
        <p>Materie: <strong><?php echo $_SESSION['ctx_materie_nume']; ?></strong></p>
        
        <?php if($mesaj) echo "<p style='color:green;'>$mesaj</p>"; ?>

        <form method="POST">
            <label>Elev:</label>
            <select name="elev_id" required>
                <?php foreach($elevi as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nume'] . " " . $e['prenume']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>Tip:</label>
            <select name="status">
                <option value="nemotivata">Nemotivată</option>
                <option value="motivata">Motivată</option>
            </select>
            
            <input type="submit" value="Salvează Absența">
        </form>
        <p><a href="ProfesorCatalog.php">⬅ Înapoi la Catalog</a></p>
    </div>
</body>
</html>