<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];
$now = date('Y-m-d H:i:s');

// Gestione inserimento offerta (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_asta']) && isset($_POST['importo'])) {
    $id_asta = intval($_POST['id_asta']);
    $importo = floatval($_POST['importo']);

    // Controlla se asta attiva e non scaduta
    $sql = "SELECT stato_asta, scadenza FROM asta WHERE id_asta = $id_asta";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) === 1) {
        $asta = mysqli_fetch_assoc($res);

        if ($asta['stato_asta'] === 'attiva' && $asta['scadenza'] > $now) {
            // Prendi offerta max attuale
            $sql2 = "SELECT MAX(importo) AS max_importo FROM offerta WHERE fk_asta = $id_asta";
            $res2 = mysqli_query($conn, $sql2);
            $max_importo = 0;
            if ($res2 && mysqli_num_rows($res2) > 0) {
                $row = mysqli_fetch_assoc($res2);
                $max_importo = floatval($row['max_importo']);
            }

            if ($importo > $max_importo) {
                // Inserisci nuova offerta
                $importo_safe = mysqli_real_escape_string($conn, $importo);
                $now_safe = mysqli_real_escape_string($conn, $now);
                $query = "INSERT INTO offerta (fk_asta, fk_utente, importo, data_offerta) VALUES ($id_asta, $id_utente, $importo_safe, '$now_safe')";
                if (mysqli_query($conn, $query)) {
                    echo "<p style='color:green;'>Offerta inserita con successo!</p>";
                } else {
                    echo "<p style='color:red;'>Errore nell'inserimento dell'offerta.</p>";
                }
            } else {
                echo "<p style='color:red;'>Devi inserire un importo superiore all'offerta massima attuale (€$max_importo)</p>";
            }
        } else {
            echo "<p style='color:red;'>Asta non attiva o scaduta.</p>";
        }
    }
}

// Recupera aste attive e non scadute
$sql = "
SELECT a.id_asta, a.data_asta, a.scadenza, a.stato_asta, p.descrizione_post, p.id_post, u.username, u.immagine_profilo,
       i.nome_immagine,
  (SELECT MAX(importo) FROM offerta WHERE fk_asta = a.id_asta) AS max_offerta,
  (SELECT fk_utente FROM offerta WHERE fk_asta = a.id_asta ORDER BY importo DESC, data_offerta ASC LIMIT 1) AS utente_vincitore
FROM asta a
JOIN post p ON a.fk_post_as = p.id_post
JOIN utenti u ON p.fk_utente_p = u.id_utente
LEFT JOIN immagini i ON i.fk_post_im = p.id_post
WHERE a.stato_asta = 'attiva' AND a.scadenza > '$now'
GROUP BY a.id_asta
ORDER BY a.scadenza ASC
";


$res = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aste Attive</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: auto; padding: 20px; }
        .asta { border: 1px solid #ccc; border-radius: 8px; padding: 15px; margin-bottom: 25px; }
        .post-img { max-width: 100%; height: auto; border-radius: 8px; }
        .info { display: flex; align-items: center; gap: 15px; }
        .profile-pic { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .btn-offerta { margin-top: 10px; }
        input[type=number] { width: 120px; padding: 5px; }
    </style>
</head>
<body>

<h1>Aste Attive</h1>

<?php
if (!$res || mysqli_num_rows($res) === 0) {
    echo "<p>Nessuna asta attiva al momento.</p>";
} else {
    while ($asta = mysqli_fetch_assoc($res)) {
        $id_asta = $asta['id_asta'];
        $desc = htmlspecialchars($asta['descrizione_post']);
        $scadenza = date('d/m/Y H:i', strtotime($asta['scadenza']));
        $username = htmlspecialchars($asta['username']);
        $img = htmlspecialchars($asta['immagine_profilo'] ?? 'uploads/default.jpg');
        $max_offerta = $asta['max_offerta'] ?? 0;
        $utente_vincitore = $asta['utente_vincitore'];
        $post_id = $asta['id_post'];

        echo "<div class='asta'>";
        echo "<div class='info'>";
        echo "<img src='$img' alt='Profilo' class='profile-pic'>";
        echo "<div><strong>$username</strong><br><small>Scadenza: $scadenza</small></div>";
        echo "</div>";
        echo "<p>$desc</p>";
        if (!empty($asta['nome_immagine'])) {
            $img_auto = htmlspecialchars($asta['nome_immagine']);
            echo "<img src='$img_auto' alt='Immagine auto' class='post-img'><br>";
        }

        echo "<p><strong>Offerta Massima: €" . number_format($max_offerta, 2) . "</strong></p>";

        if ($utente_vincitore == $id_utente) {
            echo "<p style='color:green;'>Sei attualmente il miglior offerente!</p>";
        }

        echo "
        <form method='post' action='asta.php' class='btn-offerta'>
            <input type='hidden' name='id_asta' value='$id_asta'>
            <label>La tua offerta (€):</label>
            <input type='number' name='importo' step='1000' min='" . ($max_offerta + 1000) . "' required>
            <button type='submit'>Fai offerta</button>
        </form>";

        echo "</div>";
    }
}
?>

</body>
</html>
