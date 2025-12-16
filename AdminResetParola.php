<?php
// AdminResetParola.php
require_once 'db_connect.php';
require_once 'auth_check.php';

check_authentication('admin');

$mesaj = "";

// Procesare formular
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $pass_noua = trim($_POST['parola_noua']);
    
    if ($username && $pass_noua) {
        // Criptăm parola
        $hash = password_hash($pass_noua, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("UPDATE Utilizatori SET parola_hash = :hash WHERE username = :user");
            $stmt->execute([':hash' => $hash, ':user' => $username]);
            
            if ($stmt->rowCount() > 0) {
                $mesaj = "<span style='color:green;'>Succes! Parola pentru utilizatorul '<strong>$username</strong>' a fost schimbată.</span>";
            } else {
                $mesaj = "<span style='color:red;'>Eroare: Utilizatorul nu a fost găsit sau parola este identică.</span>";
            }
        } catch (PDOException $e) {
            $mesaj = "Eroare BD: " . $e->getMessage();
        }
    } else {
        $mesaj = "Vă rugăm completați toate câmpurile.";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Resetare Parolă</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Resetare Parolă Globală</b></center></h1>
            <p>Introduceți numele de utilizator pentru a-i reseta parola.</p>
            
            <p style="text-align:center; font-weight:bold;"><?php echo $mesaj; ?></p>

            <form method="POST" action="AdminResetParola.php">
                <fieldset>
                    <legend>Resetare</legend>
                    
                    <label for="username">Username Utilizator:</label><br>
                    <input type="text" id="username" name="username" required placeholder="Ex: elev1"><br><br>
                    
                    <label for="parola_noua">Parolă Nouă:</label><br>
                    <input type="text" id="parola_noua" name="parola_noua" required placeholder="Ex: nouaparola123"><br><br>
                    
                    <input type="submit" value="Resetează Parola">
                </fieldset>
            </form>

            <p><a href="AdminDashboard.php">Înapoi la Panoul de Control</a></p>
            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>