<?php
// Pornirea sesiunii pentru a simula utilizatorul conectat
session_start();

// ----------------------------------------------------------------------
// SIMULAREA DATELOR (Înlocuiește cu interogări SQL și Sesiuni reale)
// ----------------------------------------------------------------------

// 1. Simulare autentificare (Setarea ID-ului elevului în sesiune)
// În mod normal, acest lucru s-ar face după un login reușit.
// Putem testa cu ID-ul 1 (Ioana Popescu) sau 2 (Andrei Ionescu)
$id_elev_conectat = 1; // Schimbă la 2 pentru a testa al doilea elev
$_SESSION['elev_id'] = $id_elev_conectat;

// Verifică dacă un elev este "conectat"
if (!isset($_SESSION['elev_id'])) {
    die("Eroare: Niciun elev nu este conectat. Vă rugăm să vă autentificați.");
}

$elev_id = $_SESSION['elev_id'];

// 2. Simularea bazei de date cu date pentru mai mulți elevi
$bd_elevi = [
    1 => [
        "nume" => "Popescu Ioana",
        "clasa" => "XII B",
        "an_scolar" => "2025-2026",
        "note" => [
            "Limba Română" => ["S1_Note" => [8, 9, 7], "S2_Note" => [10, 9, 9, 10], "S1_Teza" => 8, "S2_Teza" => 9],
            "Matematică" => ["S1_Note" => [6, 7, 6], "S2_Note" => [8, 7], "S1_Teza" => 7, "S2_Teza" => null],
            "Istorie" => ["S1_Note" => [9, 9, 10, 8], "S2_Note" => [9, 10], "S1_Teza" => null, "S2_Teza" => null],
        ]
    ],
    2 => [
        "nume" => "Ionescu Andrei",
        "clasa" => "XI C",
        "an_scolar" => "2025-2026",
        "note" => [
            "Limba Română" => ["S1_Note" => [6, 6, 7], "S2_Note" => [5, 6, 7], "S1_Teza" => 7, "S2_Teza" => 6],
            "Fizică" => ["S1_Note" => [10, 9], "S2_Note" => [8, 9], "S1_Teza" => null, "S2_Teza" => null],
        ]
    ]
];

// Preluarea datelor elevului conectat
$elev_curent = $bd_elevi[$elev_id] ?? die("Eroare: ID-ul elevului nu a fost găsit în baza de date.");
$nume_elev = $elev_curent['nume'];
$clasa = $elev_curent['clasa'];
$an_scolar = $elev_curent['an_scolar'];
$data_note = $elev_curent['note'];


// ----------------------------------------------------------------------
// FUNCȚII DE CALCUL (Rămân neschimbate)
// ----------------------------------------------------------------------

function calculeaza_medie_semestru($note, $teza) {
    if (empty($note) || array_sum($note) === 0) { // Adăugat verificare sumă 0
        return null;
    }
    $suma_note = array_sum($note);
    $numar_note = count($note);

    if ($teza !== null) {
        $medie_oral = $suma_note / $numar_note;
        return round((3 * $medie_oral + $teza) / 4, 2);
    } else {
        return round($suma_note / $numar_note, 2);
    }
}

function calculeaza_toate_mediile($data_note) {
    $rezultate = [];
    foreach ($data_note as $materie => $note) {
        $medie_s1 = calculeaza_medie_semestru($note['S1_Note'], $note['S1_Teza']);
        $medie_s2 = calculeaza_medie_semestru($note['S2_Note'], $note['S2_Teza']);
        
        $media_anuala = null;
        if ($medie_s1 !== null && $medie_s2 !== null) {
            $media_anuala = round(($medie_s1 + $medie_s2) / 2, 2);
        }

        $rezultate[$materie] = [
            'Medie_S1' => $medie_s1,
            'Medie_S2' => $medie_s2,
            'Media_Anuala' => $media_anuala,
        ];
    }
    return $rezultate;
}

$rezultate_medii = calculeaza_toate_mediile($data_note);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Note și Medii - <?php echo $nume_elev; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; padding: 30px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { color: #0056b3; border-bottom: 2px solid #e9ecef; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #555; font-size: 1.2em; margin-bottom: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: center; border: 1px solid #dee2e6; }
        th { background-color: #007bff; color: white; font-weight: 600; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        
        .note-list { white-space: nowrap; }
        .teza { font-weight: bold; background-color: #ffe0b2; }
        .medie-s1, .medie-s2 { background-color: #e3f2fd; font-weight: bold; }
        .medie-anuala { background-color: #c8e6c9; font-weight: bold; }
        .detalii-elev span { font-weight: bold; color: #0056b3; }
        
        .insuficient { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Catalogul Elevului</h1>

        <div class="detalii-elev">
            <h2>Elev: <span><?php echo $nume_elev; ?></span> | Clasa: <span><?php echo $clasa; ?></span> | An Școlar: <span><?php echo $an_scolar; ?></span></h2>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Materie</th>
                    <th colspan="3">Semestrul I</th>
                    <th colspan="3">Semestrul II</th>
                    <th rowspan="2">Media Anuală</th>
                </tr>
                <tr>
                    <th>Note curente</th>
                    <th class="teza">Teză S1</th>
                    <th class="medie-s1">Media S1</th>
                    <th>Note curente</th>
                    <th class="teza">Teză S2</th>
                    <th class="medie-s2">Media S2</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_note as $materie => $note): ?>
                <tr>
                    <td style="text-align: left; font-weight: 600;"><?php echo $materie; ?></td>
                    
                    <td class="note-list">
                        <?php 
                        // Afișează notele curente pentru Semestrul I
                        foreach ($note['S1_Note'] as $nota) {
                            $clasa_insuficient = ($nota < 5) ? 'insuficient' : '';
                            echo "<span class='$clasa_insuficient'>$nota</span> ";
                        }
                        ?>
                    </td>
                    <td class="teza">
                        <?php echo $note['S1_Teza'] ?? '-'; ?>
                    </td>
                    <td class="medie-s1">
                        <?php 
                        $medie_s1 = $rezultate_medii[$materie]['Medie_S1'];
                        if ($medie_s1 !== null) {
                            $clasa_insuficient = ($medie_s1 < 5) ? 'insuficient' : '';
                            echo "<span class='$clasa_insuficient'>$medie_s1</span>";
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    
                    <td class="note-list">
                        <?php 
                        // Afișează notele curente pentru Semestrul II
                        foreach ($note['S2_Note'] as $nota) {
                            $clasa_insuficient = ($nota < 5) ? 'insuficient' : '';
                            echo "<span class='$clasa_insuficient'>$nota</span> ";
                        }
                        ?>
                    </td>
                    <td class="teza">
                        <?php echo $note['S2_Teza'] ?? '-'; ?>
                    </td>
                    <td class="medie-s2">
                        <?php 
                        $medie_s2 = $rezultate_medii[$materie]['Medie_S2'];
                        if ($medie_s2 !== null) {
                            $clasa_insuficient = ($medie_s2 < 5) ? 'insuficient' : '';
                            echo "<span class='$clasa_insuficient'>$medie_s2</span>";
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>

                    <td class="medie-anuala">
                        <?php 
                        $media_anuala = $rezultate_medii[$materie]['Media_Anuala'];
                        if ($media_anuala !== null) {
                            $clasa_insuficient = ($media_anuala < 5) ? 'insuficient' : '';
                            echo "<span class='$clasa_insuficient'>$media_anuala</span>";
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p style="margin-top: 30px; font-style: italic; color: #777;">
            **Notă:** Mediile sunt calculate conform formulei standard: Media Semestrială = $\frac{3 \times Media(Oral)}{4} + \frac{Teză}{4}$, sau Media(Oral) dacă nu există teză. Media Anuală = $\frac{Media(S1) + Media(S2)}{2}$.
        </p>

    </div>
</body>
</html>