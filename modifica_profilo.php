<?php
session_start();
include "connessione.php";

// Controllo accesso
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];
$messaggio = '';

// Aggiorna dati
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cambia password
    if (!empty($_POST['nuova_password'])) {
        $nuova_password = password_hash($_POST['nuova_password'], PASSWORD_DEFAULT);
        $sql = "UPDATE utenti SET password = '$nuova_password' WHERE id_utente = $id_utente";
        if (mysqli_query($conn, $sql)) {
            $messaggio .= "Password aggiornata. ";
        } else {
            $messaggio .= "Errore aggiornando la password. ";
        }
    }

    // Carica nuova immagine
    if (!empty($_FILES['immagine']['name'])) {
        $target_dir = "uploads/";
        $nome_file = basename($_FILES["immagine"]["name"]);
        $target_file = $target_dir . $nome_file;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["immagine"]["tmp_name"]);
        if ($check !== false && in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
                $sql = "UPDATE utenti SET immagine_profilo = '$target_file' WHERE id_utente = $id_utente";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['immagine_profilo'] = $target_file;
                    $messaggio .= "Immagine aggiornata.";
                } else {
                    $messaggio .= "Errore aggiornando l'immagine nel database. ";
                }
            } else {
                $messaggio .= "Errore durante il caricamento dell'immagine. ";
            }
        } else {
            $messaggio .= "File non valido. ";
        }
    }

    if (empty($messaggio)) {
        $messaggio = "Nessuna modifica effettuata.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Profilo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .profilo {
            display: flex;
        }
        .profilo img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 30px;
        }
        .form-container {
            max-width: 400px;
        }
        form label {
            display: block;
            margin-top: 15px;
        }
        form input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
        }
        .messaggio {
            margin-top: 15px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Modifica Profilo</h2>

<div class="profilo">
    <img src="<?php echo htmlspecialchars($_SESSION['immagine_profilo'] ?? 'uploads/default.jpg'); ?>" alt="Foto Profilo">
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">

            <label for="nuova_password">Nuova Password</label>
            <input type="password" name="nuova_password" id="nuova_password" placeholder="Lascia vuoto per non cambiare">

            <label for="immagine">Nuova Immagine Profilo</label>
            <input type="file" name="immagine" id="immagine">

            <button type="submit">Salva Modifiche</button>
        </form>

        <form action="profilo.php" method="get" style="margin-top: 10px;">
            <button type="submit">Torna al profilo</button>
        </form>





        <?php if (!empty($messaggio)): ?>
            <div class="messaggio"><?php echo htmlspecialchars($messaggio); ?></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
