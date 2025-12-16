<?php
// AdminAlocareProfesor.php
require_once 'db_connect.php';
require_once 'auth_check.php';

check_authentication('admin');

$mesaj = "";

// 1. Procesare Formular (Adăugare Alocare)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO ProfesorMaterieClasa (profesor_id, materie_id, clasa_id) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['profesor_id'], $_POST['materie_id'], $_POST['clasa_id']]);
        $mesaj = "<span style='color:green;'>Alocare realizată cu succes!</span>";
    } catch (Exception $e) {
        $mesaj = "<span style='color:red;'>Eroare (probabil există deja): " . $e->getMessage() . "</span>";
    }
}

// 2. Date pentru Dropdown-uri
$profs = $pdo->query("SELECT id, nume, prenume FROM Utilizatori WHERE rol='profesor' ORDER BY nume")->fetchAll();
$materii = $pdo->query("SELECT materie_id, denumire FROM Materii ORDER BY denumire")->fetchAll();
$clase = $pdo->query("SELECT clasa_id, denumire FROM Clase ORDER BY denumire")->fetchAll();

// 3. Lista alocărilor existente (pentru tabel)
$alocari = $pdo->query("
    SELECT PMC.alocare_id, U.nume, U.prenume, M.denumire as materie, C.denumire as clasa 
    FROM ProfesorMaterieClasa PMC 
    JOIN Utilizatori U ON PMC.profesor_id = U.id 
    JOIN Materii M ON PMC.materie_id = M.materie_id 
    JOIN Clase C ON PMC.clasa_id = C.clasa_id
    ORDER BY C.denumire, M.denumire
")->fetchAll();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Alocare Profesori</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Alocare Profesori</b></center></h1>
            <p style="text-align:center; font-weight:bold;"><?php echo $mesaj; ?></p>
            
            <form method="POST" action="AdminAlocareProfesor.php">
                <fieldset>
                    <legend>Alocare Nouă</legend>
                    
                    <label>Profesor:</label><br>
                    <select name="profesor_id" required>
                        <option value="">-- Alege Profesor --</option>
                        <?php foreach ($profs as $p) echo "<option value='{$p['id']}'>{$p['nume']} {$p['prenume']}</option>"; ?>
                    </select><br><br>
                    
                    <label>Materie:</label><br>
                    <select name="materie_id" required>
                        <option value="">-- Alege Materie --</option>
                        <?php foreach ($materii as $m) echo "<option value='{$m['materie_id']}'>{$m['denumire']}</option>"; ?>
                    </select><br><br>
                    
                    <label>Clasă:</label><br>
                    <select name="clasa_id" required>
                        <option value="">-- Alege Clasă --</option>
                        <?php foreach ($clase as $c) echo "<option value='{$c['clasa_id']}'>{$c['denumire']}</option>"; ?>
                    </select><br><br>
                    
                    <input type="submit" value="Realizează Alocarea">
                </fieldset>
            </form>

            <h2>Alocări Existente</h2>
            <div class="table-responsive-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Clasă</th>
                            <th>Materie</th>
                            <th>Profesor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alocari as $a): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['clasa']); ?></td>
                            <td><?php echo htmlspecialchars($a['materie']); ?></td>
                            <td><?php echo htmlspecialchars($a['nume'] . " " . $a['prenume']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p><a href="AdminDashboard.php">Înapoi la Structură</a></p>
            <p><a href="logout.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>