<?php
// Home.php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$nume_utilizator = $_SESSION['user_nume'] ?? '';
$rol_utilizator = $_SESSION['user_role'] ?? '';
$dashboard_link = '#';

if ($is_logged_in) {
    switch ($rol_utilizator) {
        case 'elev': $dashboard_link = 'ElevCatalog.php'; break;
        case 'parinte': $dashboard_link = 'ParinteCatalog.php'; break;
        case 'profesor': $dashboard_link = 'ProfesorCatalog.php'; break;
        case 'admin': $dashboard_link = 'AdminDashboard.php'; break;
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Catalog Școlar - Acasă</title>
        <link rel="stylesheet" href="Style.css">
        <style>
            .welcome-box {
                background-color: #e8f4fd;
                border: 1px solid #b6d4fe;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                margin-bottom: 20px;
            }
            .btn-dashboard {
                display: inline-block;
                background-color: #1a5c96;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                margin-top: 10px;
            }
            .btn-dashboard:hover {
                background-color: #154a78;
                color: white;
                text-decoration: none;
            }
        </style>
    </head> 
    <body>
        <div class="main-container">
            <h1><center><b>Bine ați venit în Catalogul Școlar</b></center></h1>
            
            <?php if ($is_logged_in): ?>
                <div class="welcome-box">
                    <h2>Salut, <?php echo htmlspecialchars($nume_utilizator); ?>!</h2>
                    <p>Ești autentificat ca <strong><?php echo ucfirst($rol_utilizator); ?></strong>.</p>
                    
                    <a href="<?php echo $dashboard_link; ?>" class="btn-dashboard">Mergi la Panoul Principal</a>
                    <br><br>
                    <small>Nu ești tu? <a href="logout.php" style="color:red;">Deconectare</a></small>
                </div>

                <h2>Navigare Rapidă</h2>
                <ul>
                    <li><a href="Contact.php">Pagina de Contact</a></li>
                </ul>

            <?php else: ?>
                <p>Aceasta este pagina principală a catalogului școlar online. Aici puteți gestiona situația școlară simplu și rapid.</p>
                
                <h2>Meniul Principal</h2>
                <ul>
                    <li><a href="CatalogOnline.php"><strong>Autentificare / Vezi Catalog</strong></a></li>
                    <li><a href="Contact.php">Contact</a></li>
                </ul>

                <p>Folosiți meniul de mai sus pentru a naviga prin aplicație.</p>
            <?php endif; ?>
        </div>
    </body> 
</html>