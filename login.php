<?php
session_start();
require "connect.php";


$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = $connect->query("select * from users where username = '$username' or email = '$username'");

    if ($sql) {
        $user = $sql->fetch_assoc();
        if ($user["password"] === $password) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            
            header("Location: index.php");
            exit();
        }else {
            $error = "Nom d'utilisateur ou mot de passe incorrect";
        }
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - FitPro Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Impact', 'Arial Black', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: #000;
            border: 3px solid #ff6b00;
            border-radius: 20px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(255, 107, 0, 0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-header i {
            font-size: 4em;
            color: #ff6b00;
            margin-bottom: 20px;
        }

        .auth-header h1 {
            color: #fff;
            font-size: 2.5em;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 2px 2px 0px #ff6b00;
            margin-bottom: 10px;
        }

        .auth-header p {
            color: #999;
            font-size: 0.9em;
            font-weight: 400;
            letter-spacing: 1px;
        }

        .error-message {
            background: rgba(220, 53, 69, 0.2);
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #999;
            font-size: 0.85em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2em;
        }

        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: #1a1a1a;
            border: 2px solid #333;
            border-radius: 8px;
            color: #fff;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff6b00;
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }

        .btn {
            width: 100%;
            padding: 18px;
            background: #ff6b00;
            border: 3px solid #ff6b00;
            border-radius: 8px;
            color: white;
            font-size: 1.1em;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            box-shadow: 0 0 30px rgba(255, 107, 0, 0.6);
            transform: translateY(-2px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }

        .auth-footer p {
            color: #999;
            font-size: 0.9em;
            font-weight: 400;
        }

        .auth-footer a {
            color: #ff6b00;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .auth-footer a:hover {
            color: #ff8533;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-dumbbell"></i>
            <h1>Connexion</h1>
            <p>Accédez à votre compte FitPro Manager</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Nom d'utilisateur ou Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" required placeholder="Entrez votre nom d'utilisateur">
                </div>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" required placeholder="Entrez votre mot de passe">
                </div>
            </div>

            <button type="submit" name="login" class="btn">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>

        <div class="auth-footer">
            <p>Pas encore de compte ? <a href="signup.php">Créer un compte</a></p>
        </div>
    </div>
</body>
</html>