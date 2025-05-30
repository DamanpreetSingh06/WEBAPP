<?php
session_start();
include "connessione.php";

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];

$query = "SELECT username, immagine_profilo FROM utenti WHERE id_utente = $id_utente";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Utente non trovato";
    exit();
}

$utente = mysqli_fetch_assoc($result);
$username = $utente['username'];
$immagine = $utente['immagine_profilo'] ?? 'uploads/default.jpg';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo</title>
    <style>
        .contenitore {
            display: flex;
            align-items: flex-start;
            padding: 30px;
        }
        .foto {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 40px;
        }
        .info {
            flex-grow: 1;
        }
        .info h2 {
            margin-top: 0;
        }
        .info button {
            margin-top: 20px;
            padding: 10px 20px;
        }

        .post {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 20px 30px;
            border-radius: 10px;
            position: relative;
        }
        .post img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 5px;
            border-radius: 10px;
        }
        .menu {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .menu button {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }
        .azioni {
            display: none;
            position: absolute;
            right: 10px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 10;
        }
        .azioni form {
            display: block;
            margin: 0;
        }
        .azioni button {
            display: block;
            padding: 8px 12px;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
    </style>
    <script>
        function toggleMenu(id) {
            var menu = document.getElementById("menu-" + id);
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
    </script>
</head>
<body>

<div class="contenitore">
    <img src="<?php echo htmlspecialchars($immagine); ?>" alt="Immagine Profilo" class="foto">
    <div class="info">
        <h2><?php echo htmlspecialchars($username); ?></h2>
        <form action="modifica_profilo.php" method="get">
            <button type="submit">Edit Profile</button>
        </form>
        <form action="logout.php" method="post" style="margin-top: 10px;">
            <button type="submit">Logout</button>
        </form>
         <form action="home.php" method="get" style="margin-top: 10px;" id="home">
            <button type="submit">Torna alla Home</button>
        </form>
        <a href="post.php" style="display: inline-block; margin-top: 10px;">Crea nuovo post</a>
    </div>
</div>

<h3 style="padding: 0 30px;">I tuoi post:</h3>

<?php
$postQuery = "SELECT * FROM post WHERE fk_utente_p = $id_utente ORDER BY data_creazione DESC";
$postRes = mysqli_query($conn, $postQuery);

while ($post = mysqli_fetch_assoc($postRes)) {
    $id_post = $post['id_post'];
    $descrizione = $post['descrizione_post'];
    $like = $post['numero_like'];
    $data = $post['data_creazione'];

    echo "<div class='post'>";
    echo "<div class='menu'>";
    echo "<button onclick='toggleMenu($id_post)'>⋮</button>";
    echo "<div class='azioni' id='menu-$id_post'>";
    echo "<form method='post' action='delete_post.php'><input type='hidden' name='id_post' value='$id_post'><button type='submit'>Elimina</button></form>";
    echo "<form method='post' action='crea_asta.php'>
        <input type='hidden' name='id_post' value='$id_post'>
        <button type='submit'>Metti in asta</button>
      </form>";
    echo "</div></div>";


    


    echo "<p><strong>$data</strong></p>";
    echo "<p>$descrizione</p>";
    echo "<p>Like: $like</p>";

    // immagini
    $imgQuery = "SELECT * FROM Immagini WHERE fk_post_im = $id_post";
    $imgRes = mysqli_query($conn, $imgQuery);
    while ($img = mysqli_fetch_assoc($imgRes)) {
        echo "<img src='" . htmlspecialchars($img['nome_immagine']) . "'>";
    }

    // COMMENTI
    echo "<div class='commenti' style='margin-top:15px; padding-top:10px; border-top:1px solid #ccc;'>";

    // Recupera commenti con join per utente
    $commentQuery = "
        SELECT c.contenuto, c.data_commento, u.username, u.immagine_profilo
        FROM commenti c
        JOIN utenti u ON c.fk_utente_cm = u.id_utente
        WHERE c.fk_post_cm = $id_post
        ORDER BY c.data_commento ASC
    ";
    $commentRes = mysqli_query($conn, $commentQuery);

    while ($comment = mysqli_fetch_assoc($commentRes)) {
        $cUsername = htmlspecialchars($comment['username']);
        $cTesto = htmlspecialchars($comment['contenuto']);
        $cData = date('d/m/Y H:i', strtotime($comment['data_commento']));
        $cImg = htmlspecialchars($comment['immagine_profilo'] ?? 'uploads/default.jpg');

        echo "<div style='display:flex; align-items:center; margin-bottom:8px;'>";
        echo "<img src='$cImg' alt='$cUsername' style='width:30px; height:30px; border-radius:50%; object-fit:cover; margin-right:8px;'>";
        echo "<div><strong>$cUsername</strong> <small style='color:#666;'>$cData</small><br>$cTesto</div>";
        echo "</div>";
    }

    // Form per aggiungere commento
    echo "<form method='post' action='aggiungi_commento.php' style='margin-top:10px;'>";
    echo "<input type='hidden' name='id_post' value='$id_post'>";
    echo "<input type='text' name='contenuto' placeholder='Scrivi un commento...' required style='width:80%; padding:6px;'>";
    echo "<button type='submit' style='padding:6px 10px;'>Invia</button>";
    echo "</form>";

    echo "</div>"; // chiusura commenti div
    echo "</div>"; // chiusura post div
}

?>

</body>
</html>
