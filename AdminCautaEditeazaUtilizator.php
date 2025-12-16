<?php
// AdminCautaEditeazaUtilizator.php
require_once 'db_connect.php'; 
require_once 'auth_check.php';

// Asigură-te că doar Administratorul poate accesa această pagină
check_authentication('admin'); 

$mesaj = "";
$error = "";

// --- 1. PROCESARE ACȚIUNE (ȘTERGERE) ---
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Utilizatori WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        
        // Redirecționăm cu un mesaj de succes
        header("Location: AdminCautaEditeazaUtilizator.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        // Eroare dacă utilizatorul are date asociate în alte tabele (Foreign Key Constraint)
        $error = "Nu se poate șterge (utilizatorul are date asociate în catalog).";
    }
}

// --- 2. PROCESARE CĂUTARE ȘI AFISARE ---
$search = $_GET['search'] ?? '';
$users = [];

// Variabila care include wildcards pentru SQL (Ex: %Barbu%)
$term = "%$search%"; 

if ($search) {
    try {
        // Interogarea SQL corectă și unificată: Folosește o singură variabilă pentru 5 tipuri de căutare
        $sql = "SELECT id, username, rol, nume, prenume, email 
                FROM Utilizatori 
                WHERE nume LIKE :term 
                   OR prenume LIKE :term 
                   OR username LIKE :term 
                   -- Potrivire pe numele complet (Ex: Barbu Alexandru)
                   OR TRIM(CONCAT(nume, ' ', prenume)) LIKE :term 
                   -- Potrivire pe numele inversat (Ex: Alexandru Barbu)
                   OR TRIM(CONCAT(prenume, ' ', nume)) LIKE :term 
                ORDER BY rol, nume";
        
        $stmt = $pdo->prepare($sql);
        
        // Executăm interogarea, legând :term o singură dată
        $stmt->execute([':term' => $term]);
        $users = $stmt->fetchAll();

        if (empty($users) && $search) {
             $error = "Nu s-au găsit utilizatori pentru căutarea \"$search\".";
        }

    } catch (PDOException $e) {
        $error = "Eroare la căutarea BD: " . $e->getMessage();
    }
} else {
    // Dacă nu s-a căutat nimic, afișăm toți utilizatorii
    $users = $pdo->query("SELECT id, username, rol, nume, prenume, email FROM Utilizatori ORDER BY rol, nume LIMIT 100")->fetchAll();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Căutare Utilizatori</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Căutare și Ștergere Utilizatori</b></center></h1>
            
            <?php 
                if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
                    echo "<p style='color:green; text-align:center;'>Utilizator șters cu succes!</p>";
                }
                if ($error) {
                    echo "<p style='color:red; text-align:center;'>$error</p>";
                }
            ?>

            <form method="GET" action="AdminCautaEditeazaUtilizator.php">
                <fieldset>
                    <legend>Filtre Căutare</legend>
                    <label>Caută (Nume, Prenume sau Username):</label><br>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Ex: Barbu sau elev1"><br><br>
                    <input type="submit" value="Caută">
                </fieldset>
            </form>

            <?php if (!empty($users)): ?>
                <h2>Rezultate (Total: <?php echo count($users); ?>)</h2>
                <div class="table-responsive-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nume Complet</th>
                                <th>Username</th>
                                <th>Rol</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo htmlspecialchars($u['nume'] . " " . $u['prenume']); ?></td>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td><?php echo htmlspecialchars($u['rol']); ?></td>
                                <td>
                                    <a href="AdminCautaEditeazaUtilizator.php?delete_id=<?php echo $u['id']; ?>" 
                                       onclick="return confirm('Ești sigur că vrei să ștergi utilizatorul <?php echo $u['username']; ?>?');" 
                                       style="color:red; font-weight:bold;">Șterge</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($search): ?>
                <p style="text-align:center;">Nu s-au găsit utilizatori pentru căutarea "<?php echo htmlspecialchars($search); ?>".</p>
            <?php endif; ?>

            <p>
                <a href="AdminDashboard.php">
                    &lt;&lt; Înapoi la Panoul de Control
                </a>
            </p>
            <p><a href="logout.php" style="color:red; font-weight:bold;">Deconectare</a></p>
        </div>
    </body>
</html>