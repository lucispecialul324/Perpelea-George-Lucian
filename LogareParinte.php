<?php
// LogareParinte.php
session_start();

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'parinte') {
    header("Location: ParinteCatalog.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Catalog Scolar - Autentificare Parinte</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <div class="main-container">
            <h1><center><b>Autentificare Părinte</b></center></h1>
            <p>Vă rugăm introduceți datele de acces pentru a vizualiza situația școlară.</p>

            <?php if (isset($_GET['error'])): ?>
                <p style="color: red; text-align: center; font-weight: bold;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <fieldset>
                    <legend>Date de Login Părinte</legend>
                    
                    <label for="username">Nume Utilizator:</label><br>
                    <input type="text" id="username" name="username" required placeholder="Ex: parinte1"><br><br>
                    
                    <label for="password">Parola:</label><br>
                    <input type="password" id="password" name="password" required><br><br>
                    
                    <input type="submit" value="Logare">
                    <input type="reset" value="Anulare">
                </fieldset>
            </form>

            <p>
                <a href="CatalogOnline.php">Înapoi la pagina de autentificare</a> | 
                <a href="Home.php">Pagina Principală</a>
            </p>
        </div>
    </body>
</html>