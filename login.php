<?php
// login.php
require_once 'db_connect.php';
session_start();

// Funcție de Redirecționare bazată pe rol (pentru DRY - Don't Repeat Yourself)
function redirect_by_role($role) {
    switch ($role) {
        case 'elev':
            return "ElevCatalog.php";
        case 'parinte':
            return "ParinteCatalog.php";
        case 'profesor':
            return "ProfesorCatalog.php";
        case 'admin':
            return "AdminDashboard.php";
        default:
            return "Home.php"; // Fallback
    }
}

// 1. Verificare inițială: Dacă ești logat, te trimite la pagina ta specifică
if (isset($_SESSION['user_id'])) {
    $redirect_url = redirect_by_role($_SESSION['user_role']);
    header("Location: " . $redirect_url); 
    exit();
}

$error = null; // Variabilă pentru mesajul de eroare

// 2. Procesarea formularului de login (Metoda POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            // Căutăm utilizatorul în baza de date
            $sql = "SELECT id, parola_hash, rol, nume, prenume, clasa_id 
                    FROM Utilizatori 
                    WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch();

            // Verificăm dacă userul există ȘI dacă parola introdusă se potrivește cu hash-ul
            if ($user && password_verify($password, $user['parola_hash'])) {
                
                // --- Autentificare reușită! ---
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user_nume'] = $user['nume'] . ' ' . $user['prenume'];
                
                if ($user['rol'] === 'elev') {
                    $_SESSION['clasa_id'] = $user['clasa_id'];
                }

                // Redirecționare către panoul corect folosind funcția
                $redirect_url = redirect_by_role($user['rol']);
                header("Location: " . $redirect_url);
                exit();
            } else {
                // Login eșuat - User sau Parolă incorectă
                $error = "Nume de utilizator sau parolă incorectă.";
            }
        } catch (PDOException $e) {
            // În caz de eroare la DB
            error_log("DB Error in login: " . $e->getMessage()); // Loghează eroarea pe server
            $error = "Eroare sistem la baza de date. Vă rugăm încercați mai târziu."; 
        }
    } else {
        $error = "Vă rugăm completați toate câmpurile.";
    }
}

// 3. Afișarea erorii și redirecționarea la pagina de login (fără a afișa mult HTML aici)
if ($error) {
    // Salvăm eroarea într-o sesiune temporară și redirecționăm
    $_SESSION['login_error'] = $error;
    // Redirecționează înapoi la pagina de login unde este afișat formularul (CatalogOnline.php, Index.php, etc.)
    header("Location: CatalogOnline.php"); 
    exit();
}

// Dacă ajunge aici fără eroare (deși nu ar trebui să se întâmple decât la acces direct)
// îl trimitem la pagina principală.
header("Location: CatalogOnline.php"); 
exit();
?>