<?php
// login.php
require_once 'db_connect.php';
session_start();

// Dacă s-a trimis formularul
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Simplu mecanism de logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: CatalogOnline.html");
        exit();
    }

    if (!empty($username) && !empty($password)) {
        try {
            // Căutăm utilizatorul în baza de date
            $sql = "SELECT id, username, parola_hash, rol, nume, prenume, clasa_id FROM Utilizatori WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch();

            // Verificăm parola (folosind password_verify pentru hash-urile din DB)
            if ($user && password_verify($password, $user['parola_hash'])) {
                // Autentificare reușită! Salvăm datele în sesiune
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user_nume'] = $user['nume'] . ' ' . $user['prenume'];
                
                // Dacă e elev, salvăm și clasa direct în sesiune
                if ($user['rol'] === 'elev') {
                    $_SESSION['clasa_id'] = $user['clasa_id'];
                }

                // Redirecționare în funcție de rol
                switch ($user['rol']) {
                    case 'elev':
                        header("Location: ElevCatalog.php");
                        break;
                    case 'parinte':
                        header("Location: ParinteCatalog.php");
                        break;
                    case 'profesor':
                        header("Location: ProfesorCatalog.php");
                        break;
                    case 'admin':
                        header("Location: AdminDashboard.php"); // Sau .html dacă nu l-ai convertit încă
                        break;
                    default:
                        echo "Rol necunoscut!";
                }
                exit();
            } else {
                // Login eșuat
                $error = "Nume de utilizator sau parolă incorectă.";
            }
        } catch (PDOException $e) {
            $error = "Eroare sistem: " . $e->getMessage();
        }
    } else {
        $error = "Vă rugăm completați toate câmpurile.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Eroare Login</title><link rel="stylesheet" href="Style.css"></head>
<body>
    <div class="main-container">
        <h2 style="color:red; text-align:center;">Autentificare Eșuată</h2>
        <p style="text-align:center;"><?php echo $error ?? ''; ?></p>
        <p style="text-align:center;"><a href="CatalogOnline.html">Încearcă din nou</a></p>
    </div>
</body>
</html>