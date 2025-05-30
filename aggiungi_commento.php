<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = intval($_POST['id_post']);
    $contenuto = trim($_POST['contenuto']);
    $id_utente = $_SESSION['id_utente'];

    if ($contenuto !== '') {
        $contenuto = mysqli_real_escape_string($conn, $contenuto);
        $now = date('Y-m-d H:i:s');

        $insertQuery = "INSERT INTO commenti (contenuto, data_commento, fk_post_cm, fk_utente_cm)
                        VALUES ('$contenuto', '$now', $id_post, $id_utente)";
        mysqli_query($conn, $insertQuery);
    }
}

header("Location: home.php");  // O la pagina da cui si arriva
exit();
