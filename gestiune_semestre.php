<?php
// PHP Script pentru a simula gestionarea Semestrelor

// Lista de Semestre (Simulare date dintr-o bază de date)
$semestre = [
    ['id' => 1, 'denumire' => 'Semestrul I', 'inceput' => '2025-09-01', 'sfarsit' => '2026-01-31'],
    ['id' => 2, 'denumire' => 'Semestrul II', 'inceput' => '2026-02-01', 'sfarsit' => '2026-06-15'],
];
$mesaj_succes = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actiune']) && $_POST['actiune'] == 'adauga_semestru') {
    // Procesare adăugare semestru nou
    $denumire = htmlspecialchars($_POST['denumire']);
    $inceput = htmlspecialchars($_POST['data_inceput_nou']);
    $sfarsit = htmlspecialchars($_POST['data_sfarsit_nou']);

    // Aici s-ar face INSERT în baza de date

    $mesaj_succes = "Semestrul **$denumire** a fost adăugat cu succes (Simulare)!";

    // Adăugare la lista simulată pentru afișare
    $semestre[] = ['id' => count($semestre) + 1, 'denumire' => $denumire, 'inceput' => $inceput, 'sfarsit' => $sfarsit];
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Gestiune Semestre</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="container">
        <h2>Gestiune Semestre (Simulat)</h2>

        <?php if ($mesaj_succes): ?>
            <div class="alert alert-success"><?php echo $mesaj_succes; ?></div>
        <?php endif; ?>

        <h3>Semestre Existente (Anul Școlar 2025-2026)</h3>
        
        <?php if (empty($semestre)): ?>
            <p class="alert alert-info" style="background-color: #fff3cd; border-color: #ffeeba; color: #856404;">Nu există semestre definite pentru acest an școlar.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Denumire</th>
                        <th>Dată Început</th>
                        <th>Dată Sfârșit</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($semestre as $semestru): ?>
                        <tr>
                            <td><?php echo $semestru['id']; ?></td>
                            <td><?php echo $semestru['denumire']; ?></td>
                            <td><?php echo $semestru['inceput']; ?></td>
                            <td><?php echo $semestru['sfarsit']; ?></td>
                            <td>
                                <button class="button-action edit-btn" onclick="alert('Funcționalitate de editare pentru ID: <?php echo $semestru['id']; ?>')">Editează</button>
                                <button class="button-action delete-btn" onclick="confirm('Sigur doriți să ștergeți Semestrul <?php echo $semestru['denumire']; ?>?')">Șterge</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <hr>

        <h3>Adaugă Semestru Nou</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="actiune" value="adauga_semestru">
            
            <div class="inline-form-group">
                <div class="form-group">
                    <label for="denumire">Denumire</label>
                    <input type="text" id="denumire" name="denumire" required placeholder="Ex: Semestrul III">
                </div>

                <div class="form-group">
                    <label for="data_inceput_nou">Dată Început</label>
                    <input type="date" id="data_inceput_nou" name="data_inceput_nou" required>
                </div>

                <div class="form-group">
                    <label for="data_sfarsit_nou">Dată Sfârșit</label>
                    <input type="date" id="data_sfarsit_nou" name="data_sfarsit_nou" required>
                </div>
            </div>

            <button type="submit" class="button-primary">Adaugă Semestru</button>
            <button type="button" onclick="window.location.href='AdminSetariSistem.php';" class="button-secondary">Anulează</button>
        </form>
    </div>
</body>
</html>