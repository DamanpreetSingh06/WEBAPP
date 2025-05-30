<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Se arriva da prima richiesta (clic su "Metti in asta")
    if (isset($_POST['id_post']) && !isset($_POST['scadenza'])) {
        $id_post = intval($_POST['id_post']);

        // Verifica che il post appartenga all'utente
        $check = mysqli_query($conn, "SELECT * FROM post WHERE id_post = $id_post AND fk_utente_p = $id_utente");
        if (mysqli_num_rows($check) === 0) {
            echo "Questo post non ti appartiene!";
            exit();
        }

        // Controlla se esiste già un'asta attiva per questo post
        $checkAsta = mysqli_query($conn, "SELECT * FROM asta WHERE fk_post_as = $id_post");
        if (mysqli_num_rows($checkAsta) > 0) {
            echo "Esiste già un'asta per questo post!";
            exit();
        }

        // Mostra il form per inserire la scadenza
        echo "<h3>Inserisci scadenza per l'asta</h3>";
        echo "<form method='post' action='crea_asta.php'>
                <input type='hidden' name='id_post' value='$id_post'>
                <label>Data e ora di scadenza:</label>
                <input type='datetime-local' name='scadenza' required>
                <button type='submit'>Crea Asta</button>
              </form>";
        exit();
    }

    // Se arriva dal form con scadenza
    if (isset($_POST['id_post'], $_POST['scadenza'])) {
        $id_post = intval($_POST['id_post']);
        $scadenza = $_POST['scadenza'];
        $data_asta = date('Y-m-d H:i:s'); // Data corrente

        // Inserimento asta
        $insert = mysqli_query($conn, "INSERT INTO asta (data_asta, stato_asta, fk_post_as, fk_utente_a, scadenza)
                                       VALUES ('$data_asta', 'attiva', $id_post, $id_utente, '$scadenza')");

        if ($insert) {
            header("Location: home.php");
            exit();
        } else {
            echo "Errore nella creazione dell'asta: " . mysqli_error($conn);
        }
    }
}
?>
