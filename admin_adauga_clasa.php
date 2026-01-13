<?php
// admin_adauga_clasa.php
session_start();
require_once "db_connect.php";

// 1. VERIFICARE ACCES: Doar adminul are voie aici
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$mesaj = '';
$eroare = '';

// 2. LOGICA DE SALVARE A CLASEI
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_clasa'])) {
    $nume_clasa = trim(filter_input(INPUT_POST, 'nume_clasa', FILTER_SANITIZE_STRING));

    if (empty($nume_clasa)) {
        $eroare = "Te rugăm să introduci numele clasei.";
    } else {
        try {
            // Verificăm dacă clasa există deja pentru a nu avea duplicate
            $check = $pdo->prepare("SELECT clasa_id FROM Clase WHERE denumire = ?");
            $check->execute([$nume_clasa]);

            if ($check->rowCount() > 0) {
                $eroare = "Clasa '$nume_clasa' există deja.";
            } else {
                // Inserăm clasa în baza de date
                $sql = "INSERT INTO Clase (denumire) VALUES (?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nume_clasa]);
                $mesaj = "Succes! Clasa **$nume_clasa** a fost creată.";
            }
        } catch (PDOException $e) {
            $eroare = "Eroare SQL: " . $e->getMessage();
        }
    }
}

// 3. PRELUARE LISTĂ CLASE PENTRU AFIȘARE
$clase = $pdo->query("SELECT * FROM Clase ORDER BY denumire ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Admin - Adaugă Clasă</title>
    <link rel="stylesheet" href="Style.css">
    <style>
        .admin-container { max-width: 600px; margin: 30px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-box { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .input-field { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-add { background-color: #28a745; color: white; border: none; padding: 12px 20px; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
        .btn-add:hover { background-color: #218838; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .clase-table { width: 100%; border-collapse: collapse; }
        .clase-table th, .clase-table td { border: 1px solid #eee; padding: 12px; text-align: left; }
        .clase-table th { background: #f8f9fa; color: #333; }
    </style>
</head>
<body>

<div class="admin-container">
    <a href="admindashboard.php" style="text-decoration: none; color: #007bff; font-weight: bold;">⬅ Înapoi la Dashboard</a>
    
    <h2 style="text-align: center; color: #333;">Adăugare Clasă Nouă</h2>

    <?php if ($mesaj): ?>
        <div class="alert alert-success"><?= $mesaj ?></div>
    <?php endif; ?>

    <?php if ($eroare): ?>
        <div class="alert alert-error"><?= $eroare ?></div>
    <?php endif; ?>

    <div class="form-box">
        <form method="POST">
            <label for="nume_clasa">Denumire Clasă:</label>
            <input type="text" name="nume_clasa" id="nume_clasa" class="input-field" placeholder="Ex: 9A, 10B, 12C..." required>
            <button type="submit" name="submit_clasa" class="btn-add">Salvează Clasa</button>
        </form>
    </div>

    <h3>Clase Configurate:</h3>
    <table class="clase-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Denumire</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($clase) > 0): ?>
                <?php foreach ($clase as $c): ?>
                <tr>
                    <td><?= $c['clasa_id'] ?></td>
                    <td><strong><?= htmlspecialchars($c['denumire']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2" style="text-align:center;">Nu există clase adăugate.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>