<?php
// Contact.php
session_start();

// Verificăm dacă utilizatorul este logat pentru a ști unde îl trimitem "Înapoi"
$back_link = 'Home.php'; // Implicit: Acasă
$back_text = 'Înapoi la pagina principală';

if (isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'elev': $back_link = 'ElevCatalog.php'; break;
        case 'parinte': $back_link = 'ParinteCatalog.php'; break;
        case 'profesor': $back_link = 'ProfesorCatalog.php'; break;
        case 'admin': $back_link = 'AdminDashboard.php'; break;
    }
    $back_text = 'Înapoi la Panoul de Control';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Catalog Scolar - Contact</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Contactați-ne</b></center></h1>
            <p>Pentru orice întrebări sau asistență, vă rugăm să folosiți informațiile de contact de mai jos. Vă stăm la dispoziție de luni până vineri, între orele 8:00 și 16:00.</p>

            <h2>Detalii de Contact</h2>
            
            <ul>
                <li><strong>Adresă Email:</strong> <a href="mailto:contact@catalogscolar.ro">contact@catalogscolar.ro</a></li>
                <li><strong>Telefon:</strong> +40 734 719 742</li>
                <li><strong>Adresă Sediu:</strong> Strada Ion Minulescu, Nr. 14, Pitești</li>
            </ul>

            <p><a href="<?php echo $back_link; ?>"><?php echo $back_text; ?></a></p>
        </div>
    </body>
</html>