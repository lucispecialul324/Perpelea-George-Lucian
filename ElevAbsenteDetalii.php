<?php
// ElevAbsenteDetalii.php
require_once 'db_connect.php';
require_once 'auth_check.php';

// Verificăm permisiunea
check_authentication('elev');

$elev_id = $_SESSION['user_id'];
$nume_elev = $_SESSION['user_nume'];
$absente_rezultate = [];
$eroare = null;

try {
    // Interogare pentru absențe
    $sql = "
        SELECT 
            A.data_absenta, 
            M.denumire AS materie,
            A.status  -- 'nemotivata', 'motivata', 'in_asteptare'
        FROM Absente A
        JOIN Materii M ON A.materie_id = M.materie_id
        WHERE A.elev_utilizator_id = :elev_id
        ORDER BY A.data_absenta DESC"; // Cele mai recente primele
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':elev_id' => $elev_id]);
    $absente_rezultate = $stmt->fetchAll();

} catch (PDOException $e) {
    $eroare = "Eroare la încărcarea datelor: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Elev - Detalii Absențe</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Jurnal Absențe (<?php echo htmlspecialchars($nume_elev); ?>)</b></center></h1>

            <?php if ($eroare): ?>
                <p style="color: red; text-align:center;"><?php echo $eroare; ?></p>
            <?php elseif (empty($absente_rezultate)): ?>
                <p style="text-align:center; color:green;">Bravo! Nu ai nicio absență înregistrată.</p>
            <?php else: ?>
                
                <p>Total Absențe: <strong><?php echo count($absente_rezultate); ?></strong> 
                   (Nemotivate: <span style="color:red;"><?php echo count(array_filter($absente_rezultate, fn($a) => $a['status'] === 'nemotivata')); ?></span>)</p>

                <div class="table-responsive-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Dată și Oră</th>
                                <th>Materie</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absente_rezultate as $abs): ?>
                            <tr>
                                <td><?php echo (new DateTime($abs['data_absenta']))->format('d.m.Y H:i'); ?></td>
                                <td><?php echo htmlspecialchars($abs['materie']); ?></td>
                                <td>
                                    <?php 
                                        switch ($abs['status']) {
                                            case 'nemotivata':
                                                echo '<span style="color: red; font-weight: bold;">Nemotivată</span>';
                                                break;
                                            case 'motivata':
                                                echo '<span style="color: green; font-weight: bold;">Motivată</span>';
                                                break;
                                            default:
                                                echo '<span style="color: orange; font-weight: bold;">În Așteptare</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <p><a href="ElevCatalog.php">Înapoi la meniul elevului</a></p>
            <p><a href="login.php?logout=true" style="color:red;">Deconectare</a></p>
        </div>

        <button onclick="topFunction()" id="scrollUpButton" title="Mergi Sus">&#x25B2;</button>
        <script>
            // Scriptul tău standard pentru scroll
            window.onscroll = function() {scrollFunction()};
            function scrollFunction() {
                var mybutton = document.getElementById("scrollUpButton");
                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    mybutton.style.display = "block";
                } else {
                    mybutton.style.display = "none";
                }
            }
            function topFunction() {
                document.body.scrollTop = 0; 
                document.documentElement.scrollTop = 0; 
            }
        </script>
    </body>
</html>