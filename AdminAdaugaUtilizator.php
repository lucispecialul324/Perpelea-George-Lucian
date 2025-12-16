<?php
// AdminAdaugaUtilizator.php
require_once 'db_connect.php';
require_once 'auth_check.php';
check_authentication('admin');

$mesaj = "";

// Luăm lista de clase pentru dropdown (în caz că adăugăm un elev)
$clases = $pdo->query("SELECT clasa_id, denumire FROM Clase")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $nume = $_POST['nume'];
    $pren = $_POST['prenume'];
    $rol = $_POST['rol'];
    $mail = $_POST['email'];
    
    // Clasa e relevantă doar dacă rolul e 'elev', altfel e NULL
    $clasa_id = ($rol == 'elev' && !empty($_POST['clasa_id'])) ? $_POST['clasa_id'] : NULL;

    // Criptăm parola!
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO Utilizatori (username, parola_hash, rol, email, nume, prenume, clasa_id) 
                VALUES (:u, :p, :r, :e, :n, :pr, :c)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':u' => $user, ':p' => $hash, ':r' => $rol, ':e' => $mail, 
            ':n' => $nume, ':pr' => $pren, ':c' => $clasa_id
        ]);
        $mesaj = "<span style='color:green'>Utilizatorul $user a fost creat!</span>";
    } catch (PDOException $e) {
        $mesaj = "<span style='color:red'>Eroare: " . $e->getMessage() . "</span>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Adaugă User</title><link rel="stylesheet" href="Style.css"></head>
<body>
    <div class="main-container">
        <h1>Adaugă Utilizator</h1>
        <?php echo $mesaj; ?>
        <form method="POST">
            <fieldset>
                <label>Rol:</label>
                <select name="rol" onchange="toggleClasa(this.value)" required>
                    <option value="elev">Elev</option>
                    <option value="profesor">Profesor</option>
                    <option value="parinte">Părinte</option>
                    <option value="admin">Administrator</option>
                </select><br><br>

                <label>Username:</label><input type="text" name="username" required><br>
                <label>Parolă:</label><input type="password" name="password" required><br>
                <label>Nume:</label><input type="text" name="nume" required><br>
                <label>Prenume:</label><input type="text" name="prenume" required><br>
                <label>Email:</label><input type="email" name="email"><br><br>

                <div id="div_clasa">
                    <label>Clasa (Doar pt Elevi):</label>
                    <select name="clasa_id">
                        <option value="">-- Fără Clasă --</option>
                        <?php foreach ($clases as $c): ?>
                            <option value="<?php echo $c['clasa_id']; ?>"><?php echo $c['denumire']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div><br>

                <input type="submit" value="Creează Cont">
            </fieldset>
        </form>
        <p><a href="AdminDashboard.php">Înapoi</a></p>
    </div>
</body>
</html>