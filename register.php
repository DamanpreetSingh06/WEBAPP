<?php
session_start();
$errore = '';
if (isset($_SESSION['errore_registrazione'])) {
    $errore = $_SESSION['errore_registrazione'];
    unset($_SESSION['errore_registrazione']);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <style>
        :root {
            --primary: #4a90e2;
            --error: #e74c3c;
            --bg: #f5f7fa;
            --card-bg: #ffffff;
            --text: #333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .register-card {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: var(--primary);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #357abd;
        }

        .errore {
            color: var(--error);
            margin-top: 15px;
            font-weight: bold;
            text-align: center;
        }

        .link-login {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .link-login a {
            color: var(--primary);
            text-decoration: none;
        }

        .link-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-card" role="form" aria-labelledby="register-title">
        <form id="form" method="post" action="register_e.php" novalidate>
            <h2 id="register-title">Registrazione</h2>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" required autocomplete="username">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autocomplete="email">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required minlength="6" autocomplete="new-password">

            <label for="conferma_password">Conferma Password</label>
            <input type="password" name="conferma_password" id="conferma_password" required>

            <?php if ($errore): ?>
                <div class="errore" role="alert"><?php echo htmlspecialchars($errore); ?></div>
            <?php endif; ?>

            <button type="submit" name="registrati">Registrati</button>
        </form>

        <div class="link-login">
            Hai già un account? <a href="login.php">Accedi qui</a>
        </div>
    </div>

    <script>
        document.getElementById("form").addEventListener("submit", function(event) {
            const pass1 = document.getElementById("password").value.trim();
            const pass2 = document.getElementById("conferma_password").value.trim();
            const email = document.getElementById("email").value.trim();
            let erroreDiv = document.querySelector(".errore");

            if (erroreDiv) erroreDiv.remove();

            let errorMessage = '';

            if (pass1.length < 6) {
                errorMessage = 'La password deve contenere almeno 6 caratteri.';
            } else if (pass1 !== pass2) {
                errorMessage = 'Le password non combaciano.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorMessage = 'Inserisci un\'email valida.';
            }

            if (errorMessage) {
                event.preventDefault();
                const nuovoErrore = document.createElement('div');
                nuovoErrore.className = 'errore';
                nuovoErrore.textContent = errorMessage;
                document.querySelector("form").appendChild(nuovoErrore);
            }
        });
    </script>

</body>
</html>
