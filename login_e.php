<?php
include "connessione.php";
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM utenti WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $utente = mysqli_fetch_assoc($result);


        if (password_verify($password, $utente['password'])) {
            // Credenziali corrette
            $_SESSION['id_utente'] = $utente['id_utente'];
            $_SESSION['username'] = $utente['username'];
            $_SESSION['email'] = $utente['email'];
            $_SESSION['immagine_profilo'] = $utente['immagine_profilo'];

            header("Location: home.php");
            exit();
        } else {
            $_SESSION['errore_login'] = "Password errata";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['errore_login'] = "Username non trovato.";
        header("Location: login.php");
        exit();
    }
}
?>
