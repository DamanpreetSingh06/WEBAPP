<?php
session_start();
include "connessione.php";

// Verifica login
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];

// Recupera immagine profilo attuale per header
$query = "SELECT username, immagine_profilo FROM utenti WHERE id_utente = $id_utente";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['immagine_profilo'] = $row['immagine_profilo'] ?? 'uploads/default.jpg';
    $_SESSION['username'] = $row['username'];
} else {
    $_SESSION['immagine_profilo'] = 'uploads/default.jpg';
}

// Inizializza array dei post a cui l'utente ha messo like
if (!isset($_SESSION['liked_posts'])) {
    $_SESSION['liked_posts'] = [];
}

// Gestione toggle like se arriva POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_post'])) {
    $post_id = intval($_POST['id_post']);
    if (in_array($post_id, $_SESSION['liked_posts'])) {
        // Rimuovi like
        $update = "UPDATE post SET numero_like = GREATEST(numero_like - 1, 0) WHERE id_post = $post_id";
        $_SESSION['liked_posts'] = array_diff($_SESSION['liked_posts'], [$post_id]);
    } else {
        // Aggiungi like
        $update = "UPDATE post SET numero_like = numero_like + 1 WHERE id_post = $post_id";
        $_SESSION['liked_posts'][] = $post_id;
    }
    mysqli_query($conn, $update);
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Vintage Jatt</title>
    <style>
        /* Stessi stili di prima */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ccc;
        }
        .profilo {
            display: flex;
            align-items: center;
        }
        .profilo img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        .post {
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
            padding: 15px;
        }
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .post-header img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        .post img.post-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            margin-top: 10px;
            border-radius: 10px;
        }
        .multiple-imgs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            margin-top: 10px;
        }
        .multiple-imgs img {
            height: 200px;
            border-radius: 10px;
        }
        .descrizione {
            margin-top: 10px;
        }
        .like {
            margin-top: 10px;
            color: #888;
        }
        .like button {
            cursor: pointer;
            background: none;
            border: none;
            font-size: 16px;
            color: #888;
        }
        .like button.liked {
            color: red;
        }
    </style>
</head>
<body>

<header>
    <h2>Collezione</h2>
    <div class="profilo">
        <a href="profilo.php" style="text-decoration: none; color: black; display: flex; align-items: center;">
            <img src="<?php echo htmlspecialchars($_SESSION['immagine_profilo']); ?>" alt="Profilo">
            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </a>
    </div>
</header>

<?php
// Recupera tutti i post con info utente
$sql = "
    SELECT p.id_post, p.data_creazione, p.descrizione_post, p.numero_like, 
           u.username, u.immagine_profilo
    FROM post p
    JOIN utenti u ON p.fk_utente_p = u.id_utente
    ORDER BY p.data_creazione DESC
";
$res = mysqli_query($conn, $sql);

while ($post = mysqli_fetch_assoc($res)) {
    $id_post = $post['id_post'];
    $immagini = [];

    // Recupera immagini associate al post
    $imgQ = "SELECT nome_immagine FROM Immagini WHERE fk_post_im = $id_post";
    $imgR = mysqli_query($conn, $imgQ);
    while ($img = mysqli_fetch_assoc($imgR)) {
        $immagini[] = $img['nome_immagine'];
    }

    // Verifica se l’utente ha già messo like a questo post
    $alreadyLiked = in_array($id_post, $_SESSION['liked_posts']);

    echo "<div class='post'>";
    echo "<div class='post-header'>";
    echo "<img src='" . htmlspecialchars($post['immagine_profilo'] ?? 'uploads/default.jpg') . "' alt='Profilo'>";
    echo "<strong>" . htmlspecialchars($post['username']) . "</strong>";
    echo "</div>";

    echo "<div class='descrizione'>" . nl2br(htmlspecialchars($post['descrizione_post'])) . "</div>";

    // Bottone toggle like
    echo "<div class='like'>";
    echo "<form method='post' action='home.php'>";
    echo "<input type='hidden' name='id_post' value='$id_post'>";
    echo "<button type='submit' class='" . ($alreadyLiked ? "liked" : "") . "'>";
    echo $alreadyLiked ? "❤️ Hai già messo like" : "🤍 Metti Like";
    echo "</button>";
    echo " " . $post['numero_like'] . " like";
    echo "</form>";
    echo "</div>";

    if (count($immagini) > 1) {
        echo "<div class='multiple-imgs'>";
        foreach ($immagini as $img) {
            echo "<img src='" . htmlspecialchars($img) . "'>";
        }
        echo "</div>";
    } elseif (count($immagini) === 1) {
        echo "<img class='post-image' src='" . htmlspecialchars($immagini[0]) . "'>";
    }

    // Sezione commenti
echo "<div class='commenti' style='margin-top:15px; padding-top:10px; border-top:1px solid #ccc;'>";

// Recupera i commenti del post con username e immagine profilo
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
echo "<form method='post' action='aggiungi_commento.php' style='margin-top:10px; display:flex; gap:8px;'>";
echo "<input type='hidden' name='id_post' value='$id_post'>";
echo "<input type='text' name='contenuto' placeholder='Scrivi un commento...' required style='flex-grow:1; padding:6px;'>";
echo "<button type='submit' style='padding:6px 10px;'>Invia</button>";
echo "</form>";

echo "</div>"; // chiusura commenti


    echo "</div>";
}
?>

</body>
</html>