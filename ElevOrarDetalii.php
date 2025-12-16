<?php
// ElevOrarDetalii.php
require_once 'db_connect.php';
// Presupunem că 'auth_check.php' doar verifică sesiunea și rolul
require_once 'auth_check.php'; 

// Verificăm dacă utilizatorul este logat și are rolul 'elev'
// Dacă 'auth_check.php' nu face asta, adaugă aici verificarea:
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'elev') { header("Location: login.php"); exit; }

// --- CORECTARE LOGICĂ DE PRELUARE CLASĂ ---
$user_id = $_SESSION['user_id'];
$nume_elev = $_SESSION['user_nume'] ?? 'Elev';
$clasa_id = null; // Vom prelua ID-ul clasei din DB, nu din sesiune direct.

$orar_organizat = [];
$ore_posibile = []; 
$eroare = "";

try {
    // 1. PRELUARE ID CLASĂ PE BAZA USER ID-ULUI (100% sigur)
    $sql_clasa = "
        SELECT U.clasa_id 
        FROM Utilizatori U
        WHERE U.id = :user_id
    ";
    $stmt_clasa = $pdo->prepare($sql_clasa);
    $stmt_clasa->execute([':user_id' => $user_id]);
    $clasa_data = $stmt_clasa->fetch();

    if ($clasa_data && $clasa_data['clasa_id'] !== null) {
        $clasa_id = $clasa_data['clasa_id'];
    } else {
        // Dacă nu găsim ID-ul clasei, nu putem afișa orarul
        $eroare = "Elevul nu este alocat unei clase sau clasa nu există.";
        $clasa_id = 0; // Setăm la 0 pentru a opri interogarea ulterioară
    }
    // ----------------------------------------------------


    // 2. Selectăm orarul pentru clasa elevului
    if ($clasa_id > 0) {
        $sql = "
            SELECT 
                O.ziua_saptamanii, 
                DATE_FORMAT(O.ora_inceput, '%H:%i') as ora_formatata,
                M.denumire as materie
            FROM Orar O
            JOIN Materii M ON O.materie_id = M.materie_id /* Corectat m.materie_id din schema ta */
            WHERE O.clasa_id = :clasa_id
            ORDER BY O.ora_inceput ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':clasa_id' => $clasa_id]);
        $rezultate = $stmt->fetchAll();

        // Organizăm datele pentru afișare (Matrice: Ora -> Zi -> Materie)
        foreach ($rezultate as $rand) {
            $ora = $rand['ora_formatata'];
            $zi = $rand['ziua_saptamanii'];
            
            $orar_organizat[$ora][$zi] = $rand['materie'];
            
            if (!in_array($ora, $ore_posibile)) {
                $ore_posibile[] = $ora;
            }
        }
        // Sortăm orele cronologic
        sort($ore_posibile);
    }

} catch (PDOException $e) {
    $eroare = "Eroare la baza de date: " . $e->getMessage();
}

$zile_saptamanii = ['Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri'];
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Elev - Orar Săptămânal</title>
        <link rel="stylesheet" href="Style.css">
        <style>
            /* Stiluri pentru a face tabelul să arate bine, presupunând că Style.css nu le are */
            .main-container { max-width: 900px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            h1 { color: #007bff; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 30px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
            thead th { background-color: #007bff; color: white; }
            tbody td:first-child { background-color: #f0f0f0; font-weight: bold; }
            .error-message { color: red; text-align: center; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Orar Săptămânal</b></center></h1>
            
            <?php if ($eroare): ?>
                <p class="error-message"><?= htmlspecialchars($eroare) ?></p>
            <?php elseif (empty($ore_posibile)): ?>
                <p style="text-align:center;">Orarul nu a fost încă definit pentru clasa ta.</p>
            <?php else: ?>
                <p style="text-align:center; margin-bottom: 20px;">Orarul pentru elevul **<?= htmlspecialchars($nume_elev) ?>**.</p>
                <div class="table-responsive-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 10%;">Ora</th>
                                <?php foreach ($zile_saptamanii as $zi): ?>
                                    <th style="width: 18%;"><?php echo $zi; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ore_posibile as $ora): ?>
                            <tr>
                                <td><?php echo $ora; ?></td>
                                
                                <?php foreach ($zile_saptamanii as $zi): ?>
                                    <td>
                                        <?php 
                                            // Afișăm materia sau o liniuță dacă e liber
                                            echo htmlspecialchars($orar_organizat[$ora][$zi] ?? '-'); 
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <p style="margin-top: 20px;"><a href="ElevCatalog.php">Înapoi la meniul elevului</a></p>
            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>
    </body>
</html>