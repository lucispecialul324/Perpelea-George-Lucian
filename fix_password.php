<?php
// fix_password.php
require_once 'db_connect.php';

// Parola pe care vrem să o setăm
$username_target = 'elev1';
$parola_noua = 'parola123';

// Generăm hash-ul corect folosind funcția serverului tău
$hash_nou = password_hash($parola_noua, PASSWORD_DEFAULT);

try {
    // Actualizăm utilizatorul în baza de date
    $stmt = $pdo->prepare("UPDATE Utilizatori SET parola_hash = :hash WHERE username = :user");
    $stmt->execute([
        ':hash' => $hash_nou,
        ':user' => $username_target
    ]);

    echo "<h1>Succes!</h1>";
    echo "<p>Parola pentru utilizatorul <strong>$username_target</strong> a fost resetată la: <strong>$parola_noua</strong></p>";
    echo "<p>Noul hash generat este: $hash_nou</p>";
    echo "<br><a href='LogareStudent.html'>Mergi la Autentificare</a>";

} catch (PDOException $e) {
    echo "Eroare la actualizare: " . $e->getMessage();
}
?>