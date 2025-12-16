<?php
// fix_password_all.php
require_once 'db_connect.php';

// 1. Definim parola comună pentru toți
$parola_noua = 'parola123';

// 2. Generăm hash-ul valid
$hash_nou = password_hash($parola_noua, PASSWORD_DEFAULT);

try {
    // 3. Executăm UPDATE pe TOATĂ tabela (fără clauza WHERE)
    $sql = "UPDATE Utilizatori SET parola_hash = :hash";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hash' => $hash_nou]);
    
    // Vedem câți utilizatori au fost afectați
    $nr_utilizatori = $stmt->rowCount();

    echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: green;'>Succes!</h1>";
    echo "<p>Am actualizat parola pentru <strong>$nr_utilizatori</strong> utilizatori.</p>";
    echo "<p>Noua parolă pentru TOȚI (Admin, Profesori, Elevi, Părinți) este: <br><strong style='font-size: 20px;'>$parola_noua</strong></p>";
    echo "<br><br>";
    echo "<a href='CatalogOnline.php' style='padding: 10px 20px; background: #1a5c96; color: white; text-decoration: none; border-radius: 5px;'>Mergi la Logare</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Eroare: " . $e->getMessage();
}
?>