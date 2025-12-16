<?php
// PHP Script pentru a simula gestionarea Anului Școlar

// Inițializare variabile (Acestea ar trebui preluate dintr-o bază de date reală)
$an_curent_bd = "2025-2026";
$data_inceput_bd = "2025-09-01";
$data_sfarsit_bd = "2026-08-31";
$statut_bd = "Activ";
$mesaj_succes = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aici se procesează datele trimise (de exemplu, salvarea în baza de date)
    $an_selectat = htmlspecialchars($_POST['an_scolar']);
    $data_inceput = htmlspecialchars($_POST['data_inceput']);
    $data_sfarsit = htmlspecialchars($_POST['data_sfarsit']);

    $mesaj_succes = "Anul școlar a fost salvat (Simulare): **$an_selectat** (Început: $data_inceput, Sfârșit: $data_sfarsit)";

    // Actualizarea valorilor simulate pentru afișare
    $an_curent_bd = $an_selectat;
    $data_inceput_bd = $data_inceput;
    $data_sfarsit_bd = $data_sfarsit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Setează Anul Școlar Curent</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="container" style="max-width: 650px;">
        <h2>Setează Anul Școlar Curent (Simulat)</h2>

        <?php if ($mesaj_succes): ?>
            <div class="alert alert-success"><?php echo $mesaj_succes; ?></div>
        <?php endif; ?>

        <div class="alert alert-info">
            Anul Școlar Curent (BD): **<?php echo $an_curent_bd; ?>** (Statut: <?php echo $statut_bd; ?>)
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="an_scolar">An Școlar</label>
                <select id="an_scolar" name="an_scolar" required>
                    <option value="2024-2025">2024-2025</option>
                    <option value="2025-2026" <?php if ($an_curent_bd == "2025-2026") echo "selected"; ?>>2025-2026</option>
                    <option value="2026-2027">2026-2027</option>
                </select>
            </div>

            <div class="form-group">
                <label for="data_inceput">Dată Început</label>
                <input type="date" id="data_inceput" name="data_inceput" value="<?php echo $data_inceput_bd; ?>" required>
            </div>

            <div class="form-group">
                <label for="data_sfarsit">Dată Sfârșit</label>
                <input type="date" id="data_sfarsit" name="data_sfarsit" value="<?php echo $data_sfarsit_bd; ?>" required>
            </div>

            <div class="button-group">
                <button type="submit" class="button-primary">Salvați & Setați ca Activ</button>
                <button type="button" onclick="window.location.href='AdminSetariSistem.php';" class="button-secondary">Anulează</button>
            </div>
        </form>
    </div>
</body>
</html>