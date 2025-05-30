<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['id_post'])) {
    $id_post = intval($_POST['id_post']);

    // Inizializza array like se non esiste
    if (!isset($_SESSION['liked_posts'])) {
        $_SESSION['liked_posts'] = [];
    }

    // Se il post NON è stato già likato dall'utente
    if (!in_array($id_post, $_SESSION['liked_posts'])) {
        $query = "UPDATE post SET numero_like = numero_like + 1 WHERE id_post = $id_post";
        mysqli_query($conn, $query);

        // Salva il like nella sessione
        $_SESSION['liked_posts'][] = $id_post;
    }
}

header("Location: home.php");
exit();
