<?php
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('profesor');

$profesor_id = $_SESSION['user_id'];
$nume_profesor = $_SESSION['user_nume'];
$clase_alocate = [];
$mesaj = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['context'])) {
    list($cid, $mid) = explode('_', $_POST['context']);
    $_SESSION['ctx_clasa_id'] = $cid;
    $_SESSION['ctx_materie_id'] = $mid;
    
    $stmt = $pdo->prepare("SELECT denumire FROM Clase WHERE clasa_id = ?");
    $stmt->execute([$cid]);
    $_SESSION['ctx_clasa_nume'] = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT denumire FROM Materii WHERE materie_id = ?");
    $stmt->execute([$mid]);
    $_SESSION['ctx_materie_nume'] = $stmt->fetchColumn();
    
    $mesaj = "Ați activat: " . $_SESSION['ctx_clasa_nume'] . " - " . $_SESSION['ctx_materie_nume'];
}

try {
    $sql = "SELECT PMC.clasa_id, C.denumire as clasa, PMC.materie_id, M.denumire as materie
            FROM ProfesorMaterieClasa PMC
            JOIN Clase C ON PMC.clasa_id = C.clasa_id
            JOIN Materii M ON PMC.materie_id = M.materie_id
            WHERE PMC.profesor_id = :pid
            ORDER BY C.denumire";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $profesor_id]);
    $clase_alocate = $stmt->fetchAll();
} catch (Exception $e) { $mesaj = "Eroare: " . $e->getMessage(); }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profesor - Gestiune</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Profesor: <?php echo htmlspecialchars($nume_profesor); ?></b></center></h1>
            
            <?php if($mesaj) echo "<p style='color:green; text-align:center; font-weight:bold;'>$mesaj</p>"; ?>

            <div style="background:#eef2f7; padding:20px; border-radius:8px; margin-bottom:20px; border:1px solid #ccc;">
                <form method="POST">
                    <label><strong>Selectează Clasa și Materia:</strong></label><br>
                    <select name="context" required>
                        <option value="">-- Alege din listă --</option>
                        <?php foreach ($clase_alocate as $rand): ?>
                            <option value="<?php echo $rand['clasa_id'] . '_' . $rand['materie_id']; ?>">
                                Clasa <?php echo htmlspecialchars($rand['clasa']); ?> (<?php echo htmlspecialchars($rand['materie']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="Activează Clasa">
                </form>
            </div>

            <?php if (isset($_SESSION['ctx_clasa_id'])): ?>
                <h2>Acțiuni pentru: <span style="color:#4CAF50;"><?php echo $_SESSION['ctx_clasa_nume'] . ' (' . $_SESSION['ctx_materie_nume'] . ')'; ?></span></h2>
                <ul>
                    <li><a href="ProfesorAdaugaNota.php">Adăugare Notă</a></li>
                    <li><a href="ProfesorAdaugaAbsenta.php">Înregistrare Absență</a></li>
                    <li><a href="ProfesorVizualizareMedii.php">Vizualizare Medii</a></li>
                    <li><a href="ProfesorRapoarteClasa.php">Rapoarte Clasă</a></li>
                </ul>
            <?php else: ?>
                <p><em>Vă rugăm să selectați o clasă de mai sus pentru a debloca meniul de acțiuni.</em></p>
            <?php endif; ?>

            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>