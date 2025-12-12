<?php
session_start();
require "connect.php";

// Si déjà connecté, rediriger vers index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Traiter le formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (strlen($username) < 3) {
        $error = "Le nom d'utilisateur doit contenir au moins 3 caractères";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $connect->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Ce nom d'utilisateur ou cet email existe déjà";
        } else {
            // Hasher le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insérer le nouvel utilisateur
            $stmt = $connect->prepare("INSERT INTO users (username, email, full_name, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $full_name, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Compte créé avec succès ! Redirection...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Erreur lors de la création du compte";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - FitPro Manager</title>
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
            max-width: 500px;
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

        .success-message {
            background: rgba(40, 167, 69, 0.2);
            border: 2px solid #28a745;
            color: #28a745;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
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
            <i class="fas fa-user-plus"></i>
            <h1>Inscription</h1>
            <p>Créez votre compte FitPro Manager</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Nom complet</label>
                <div class="input-wrapper">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="full_name" required placeholder="Entrez votre nom complet">
                </div>
            </div>

            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" required placeholder="Choisissez un nom d'utilisateur">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" required placeholder="Entrez votre email">
                </div>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" required placeholder="Choisissez un mot de passe">
                </div>
            </div>

            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" required placeholder="Confirmez le mot de passe">
                </div>
            </div>

            <button type="submit" name="signup" class="btn">
                <i class="fas fa-user-plus"></i> Créer mon compte
            </button>
        </form>

        <div class="auth-footer">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</body>
</html>