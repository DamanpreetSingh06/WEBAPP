<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_asta'], $_POST['importo'])) {
    $id_asta = intval($_POST['id_asta']);
    $importo = floatval($_POST['importo']);
    $now = date('Y-m-d H:i:s');

    // Verifica se asta è valida
    $check = mysqli_query($conn, "SELECT * FROM asta WHERE id_asta = $id_asta AND stato_asta = 'attiva' AND scadenza > '$now'");
    if (mysqli_num_rows($check) === 1) {
        $stmt = mysqli_prepare($conn, "INSERT INTO offerte (fk_asta, fk_utente, importo, data_offerta) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iids", $id_asta, $id_utente, $importo, $now);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: aste.php");
exit();
