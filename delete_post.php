<?php
session_start();
include "connessione.php";

// Verifica che l'utente sia loggato
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

// Verifica che il POST contenga un id_post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_post'])) {
    $id_post = intval($_POST['id_post']);
    $id_utente = $_SESSION['id_utente'];

    // Controlla che il post appartenga all'utente loggato
    $checkQuery = "SELECT * FROM post WHERE id_post = $id_post AND fk_utente_p = $id_utente";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) === 1) {
        // Cancella prima le immagini associate (se vuoi mantenere il DB pulito)
        $deleteImages = "DELETE FROM Immagini WHERE fk_post_im = $id_post";
        mysqli_query($conn, $deleteImages);

        // Poi cancella eventuali commenti
        $deleteComments = "DELETE FROM commenti WHERE fk_post_cm = $id_post";
        mysqli_query($conn, $deleteComments);

        // Cancella anche eventuali aste collegate (opzionale)
        $deleteAsta = "DELETE FROM asta WHERE fk_post_as = $id_post";
        mysqli_query($conn, $deleteAsta);

        // Cancella infine il post
        $deletePost = "DELETE FROM post WHERE id_post = $id_post";
        if (mysqli_query($conn, $deletePost)) {
            header("Location: profilo.php");
            exit();
        } else {
            echo "Errore durante l'eliminazione del post.";
        }
    } else {
        echo "Post non trovato o non autorizzato.";
    }
} else {
    echo "Richiesta non valida.";
}
?>
