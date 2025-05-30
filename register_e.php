<?php
include "connessione.php";
session_start();

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    try {
        // Controllo se username già esiste
        $checkUsername = "SELECT * FROM utenti WHERE username = '$username'";
        $resUsername = mysqli_query($conn, $checkUsername);

        // Controllo se email già esiste
        $checkEmail = "SELECT * FROM utenti WHERE email = '$email'";
        $resEmail = mysqli_query($conn, $checkEmail);

        if (mysqli_num_rows($resUsername) > 0) {
            $_SESSION['errore_registrazione'] = "Username già in uso. Scegli un altro username.";
            header("Location: register.php");
            exit();
        } elseif (mysqli_num_rows($resEmail) > 0) {
            $_SESSION['errore_registrazione'] = "Email già registrata. Usa un'altra email.";
            header("Location: register.php");
            exit();
        } else {
            // Hash della password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Inserimento utente
            $insertUser = "INSERT INTO utenti (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

            if (mysqli_query($conn, $insertUser)) {
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['id_utente'] = mysqli_insert_id($conn);
                $_SESSION['immagine_profilo'] = $utente['immagine_profilo'];


                header("Location: home.php");
                exit();
            } else {
                $_SESSION['errore_registrazione'] = "Errore durante la registrazione. Riprova.";
                header("Location: register.php");
                exit();
            }
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['errore_registrazione'] = "Errore: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>
