<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];
$messaggio = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descrizione = mysqli_real_escape_string($conn, $_POST['descrizione']);
    $prezzo_base = floatval($_POST['prezzo_base']);
    $data_creazione = date('Y-m-d');

    if (empty($descrizione)) {
        $messaggio = "La descrizione è obbligatoria.";
    } elseif ($prezzo_base <= 0) {
        $messaggio = "Il prezzo deve essere maggiore di zero.";
    } elseif (empty($_FILES['immagini']['name'][0])) {
        $messaggio = "Carica almeno una immagine.";
    } else {
        $sql_post = "INSERT INTO post (data_creazione, numero_like, descrizione_post, prezzo_base, numero_offerte, fk_utente_p) 
                     VALUES ('$data_creazione', 0, '$descrizione', $prezzo_base, 0, $id_utente)";

        if (mysqli_query($conn, $sql_post)) {
            $id_post = mysqli_insert_id($conn);
            $target_dir = "uploads/";
            $tutte_ok = true;

            foreach ($_FILES['immagini']['tmp_name'] as $key => $tmp_name) {
                $nome_file = basename($_FILES['immagini']['name'][$key]);
                $imageFileType = strtolower(pathinfo($nome_file, PATHINFO_EXTENSION));
                $target_file = $target_dir . time() . "_" . rand(1000, 9999) . "_" . $nome_file;

                $check = getimagesize($tmp_name);
                if ($check !== false && in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $sql_img = "INSERT INTO Immagini (nome_immagine, fk_post_im) VALUES ('$target_file', $id_post)";
                        if (!mysqli_query($conn, $sql_img)) {
                            $tutte_ok = false;
                            $messaggio = "Errore inserendo immagine nel database.";
                            break;
                        }
                    } else {
                        $tutte_ok = false;
                        $messaggio = "Errore nel caricamento dell'immagine.";
                        break;
                    }
                } else {
                    $tutte_ok = false;
                    $messaggio = "File non valido. Solo JPG, JPEG, PNG.";
                    break;
                }
            }

            if ($tutte_ok) {
                $messaggio = "Post creato con successo!";
            }
        } else {
            $messaggio = "Errore durante la creazione del post.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Crea Post</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #2980b9;
            --error: #e74c3c;
            --success: #27ae60;
            --bg: #f0f4f8;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg);
            padding: 20px;
        }

        .container {
            background-color: white;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: var(--primary);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1c6ea4;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: var(--success);
            color: white;
        }

        .message.error {
            background-color: var(--error);
            color: white;
        }

        .links {
            text-align: center;
            margin-top: 30px;
        }

        .links a {
            text-decoration: none;
            color: var(--accent);
            margin: 0 10px;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Crea un nuovo post</h2>

    <?php if ($messaggio): ?>
        <div class="message <?php echo strpos($messaggio, 'successo') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($messaggio); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="postForm">
        <label for="descrizione">Titolo / Descrizione</label>
        <input type="text" name="descrizione" id="descrizione" required>

        <label for="prezzo_base">Prezzo Base (€)</label>
        <input type="number" step="0.01" min="0.01" name="prezzo_base" id="prezzo_base" required>

        <label for="immagini">Carica Immagini (JPG, JPEG, PNG)</label>
        <input type="file" name="immagini[]" id="immagini" accept=".jpg,.jpeg,.png" multiple required>

        <button type="submit">Pubblica</button>
    </form>

    <div class="links">
        <a href="profilo.php">Torna al Profilo</a> |
        <a href="home.php">Vai alla Home</a>
    </div>
</div>

<script>
    document.getElementById("postForm").addEventListener("submit", function(e) {
        const prezzo = parseFloat(document.getElementById("prezzo_base").value);
        const files = document.getElementById("immagini").files;
        if (prezzo <= 0) {
            alert("Il prezzo base deve essere maggiore di zero.");
            e.preventDefault();
        } else if (files.length === 0) {
            alert("Carica almeno un'immagine.");
            e.preventDefault();
        }
    });
</script>

</body>
</html>
