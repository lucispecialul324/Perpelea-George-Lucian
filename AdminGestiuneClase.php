<?php
// AdminGestiuneClase.php
require_once 'db_connect.php'; 
require_once 'auth_check.php';

// Asigură-te că doar Administratorul poate accesa această pagină
check_authentication('admin'); 

$clase = [];
$profesori = [];
$error = "";
$success = "";

// 1. PRELUARE LISTĂ PROFESORI (pentru dropdown-ul de diriginte)
try {
    $stmt_prof = $pdo->query("SELECT id, nume, prenume FROM Utilizatori WHERE rol = 'profesor' ORDER BY nume, prenume");
    $profesori = $stmt_prof->fetchAll();
} catch (PDOException $e) {
    $error = "Eroare la încărcarea profesorilor: " . $e->getMessage();
}

// 2. LOGICA DE ADĂUGARE CLASĂ NOUĂ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adauga_clasa'])) {
    $nume_clasa = trim($_POST['denumire']);
    $profil_clasa = trim($_POST['profil']);
    $diriginte_id = !empty($_POST['diriginte_id']) ? $_POST['diriginte_id'] : null;

    if (empty($nume_clasa)) {
        $error = "Numele clasei este obligatoriu.";
    } else {
        try {
            // Verificăm dacă clasa există deja
            $check = $pdo->prepare("SELECT clasa_id FROM Clase WHERE denumire = ?");
            $check->execute([$nume_clasa]);

            if ($check->rowCount() > 0) {
                $error = "Clasa '$nume_clasa' există deja!";
            } else {
                // Inserăm clasa nouă cu dirigintele selectat
                $sql_insert = "INSERT INTO Clase (denumire, profil, diriginte_id) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql_insert);
                $stmt->execute([$nume_clasa, $profil_clasa, $diriginte_id]);
                $success = "Clasa a fost adăugată cu succes!";
            }
        } catch (PDOException $e) {
            $error = "Eroare la salvare: " . $e->getMessage();
        }
    }
}

// 3. PRELUARE LISTĂ CLASE PENTRU TABEL
try {
    $sql = "SELECT 
                C.denumire, 
                C.profil, 
                U.nume, 
                U.prenume 
            FROM Clase C 
            LEFT JOIN Utilizatori U ON C.diriginte_id = U.id
            ORDER BY C.denumire";
            
    $clase = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $error = "Eroare la încărcarea claselor: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Gestiune Clase</title>
        <link rel="stylesheet" href="Style.css">
        <style>
            .form-section { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 30px; }
            .form-group { margin-bottom: 15px; }
            .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
            .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
            .btn-save { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
            .btn-save:hover { background-color: #218838; }
            .msg-success { color: green; font-weight: bold; text-align: center; padding: 10px; background: #e8f5e9; border-radius: 5px; }
            .msg-error { color: red; font-weight: bold; text-align: center; padding: 10px; background: #ffebee; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Gestiune Clase</b></center></h1>
            
            <?php if ($error): ?><p class="msg-error"><?php echo $error; ?></p><?php endif; ?>
            <?php if ($success): ?><p class="msg-success"><?php echo $success; ?></p><?php endif; ?>

            <div class="form-section">
                <h3>Adaugă o Clasă Nouă</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Denumire Clasă:</label>
                        <input type="text" name="denumire" required placeholder="ex: 9A">
                    </div>
                    <div class="form-group">
                        <label>Profil:</label>
                        <input type="text" name="profil" placeholder="ex: Mate-Info">
                    </div>
                    <div class="form-group">
                        <label>Alege Dirigintele:</label>
                        <select name="diriginte_id">
                            <option value="">-- Fără Diriginte (Momentan) --</option>
                            <?php foreach ($profesori as $p): ?>
                                <option value="<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['nume'] . ' ' . $p['prenume']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="adauga_clasa" class="btn-save">Salvează Clasa</button>
                </form>
            </div>

            <hr>

            <div class="table-responsive-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Clasa</th>
                            <th>Profil</th>
                            <th>Diriginte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clase)): ?>
                            <tr><td colspan="3" style="text-align:center;">Nu există clase definite.</td></tr>
                        <?php else: ?>
                            <?php foreach ($clase as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['denumire']); ?></td>
                                <td><?php echo htmlspecialchars($c['profil']); ?></td>
                                <td>
                                    <?php 
                                        if ($c['nume']) {
                                            echo htmlspecialchars($c['nume'] . ' ' . $c['prenume']); 
                                        } else {
                                            echo '<span style="color:red;">Fără Diriginte</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p><a href="AdminDashboard.php">Înapoi la Panoul de Control</a></p>
            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>