<?php
session_start();
$errore = '';
if (isset($_SESSION['errore_login'])) {
    $errore = $_SESSION['errore_login'];
    unset($_SESSION['errore_login']);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #4a90e2;
            --error-color: #e74c3c;
            --background: #f9f9f9;
            --card-bg: #fff;
            --text-color: #333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
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
            color: var(--error-color);
            margin-top: 12px;
            font-weight: bold;
            text-align: center;
        }

        .link-register {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .link-register a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .link-register a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card" role="form" aria-labelledby="login-title">
        <form method="post" action="login_e.php">
            <h2 id="login-title">Login</h2>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" required autocomplete="username">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required autocomplete="current-password">

            <?php if ($errore): ?>
                <div class="errore" role="alert"><?php echo htmlspecialchars($errore); ?></div>
            <?php endif; ?>

            <button type="submit">Accedi</button>
        </form>

        <div class="link-register">
            Non hai un account? <a href="register.php">Registrati qui</a>
        </div>
    </div>

</body>
</html>
