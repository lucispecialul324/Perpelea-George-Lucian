<?php
// ProfesorAdaugaNota.php
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('profesor');

if (!isset($_SESSION['ctx_clasa_id'])) {
    header("Location: ProfesorCatalog.php"); // Dacă nu a selectat clasa, înapoi
    exit();
}

$clasa_id = $_SESSION['ctx_clasa_id'];
$materie_id = $_SESSION['ctx_materie_id'];
$profesor_id = $_SESSION['user_id'];
$status_msg = "";

// PROCESARE FORMULAR
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $elev_id = $_POST['elev_id'];
    $nota = $_POST['nota'];
    $data = $_POST['data'];

    try {
        $sql = "INSERT INTO Note (elev_utilizator_id, profesor_utilizator_id, materie_id, valoare, data_nota) 
                VALUES (:eid, :pid, :mid, :val, :dat)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':eid' => $elev_id,
            ':pid' => $profesor_id,
            ':mid' => $materie_id,
            ':val' => $nota,
            ':dat' => $data
        ]);
        $status_msg = "<span style='color:green'>Nota $nota a fost adăugată cu succes!</span>";
    } catch (PDOException $e) {
        $status_msg = "<span style='color:red'>Eroare: " . $e->getMessage() . "</span>";
    }
}

// EXTRAGERE ELEVI DIN CLASA SELECTATA
$stmt = $pdo->prepare("SELECT id, nume, prenume FROM Utilizatori WHERE clasa_id = :cid AND rol='elev' ORDER BY nume");
$stmt->execute([':cid' => $clasa_id]);
$elevi = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Adaugă Notă</title><link rel="stylesheet" href="Style.css"></head>
<body>
    <div class="main-container">
        <h1>Adaugă Notă</h1>
        <p>Materia: <strong><?php echo $_SESSION['ctx_materie_nume']; ?></strong> | Clasa: <strong><?php echo $_SESSION['ctx_clasa_nume']; ?></strong></p>
        
        <?php echo $status_msg; ?>

        <form method="POST">
            <fieldset>
                <label>Elev:</label>
                <select name="elev_id" required>
                    <?php foreach ($elevi as $elev): ?>
                        <option value="<?php echo $elev['id']; ?>"><?php echo htmlspecialchars($elev['nume'] . ' ' . $elev['prenume']); ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <label>Nota (1-10):</label>
                <input type="number" name="nota" min="1" max="10" step="0.01" required><br><br>

                <label>Data:</label>
                <input type="date" name="data" value="<?php echo date('Y-m-d'); ?>" required><br><br>

                <input type="submit" value="Salvează Nota">
            </fieldset>
        </form>
        <p><a href="ProfesorCatalog.php">Înapoi</a></p>
    </div>
</body>
</html>