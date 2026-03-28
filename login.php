<?php
session_start();

if (isset($_SESSION['utilisateur'])) {
    header('Location: catalogue.php');
    exit;
}

require_once 'connexion.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login      = trim($_POST['login']);
    $motDePasse = trim($_POST['mot_de_passe']);

    $pdo  = getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = ?");
    $stmt->execute([$login]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
        $_SESSION['utilisateur'] = $utilisateur['login'];
        $_SESSION['role']        = $utilisateur['role']; // ✅ Stockage du rôle

        // ✅ Cookie dernière connexion (30 jours)
        setcookie(
            'derniere_connexion',
            date('d/m/Y H:i'),
            time() + 30 * 24 * 3600
        );

        header('Location: catalogue.php');
        exit;
    } else {
        $erreur = "Login ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – CinemaBD</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            font-family: 'Segoe UI', sans-serif;
        }
        .login-box {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 45px rgba(0,0,0,0.5);
            color: white;
        }
        .login-box .logo { text-align: center; margin-bottom: 30px; font-size: 48px; }
        .login-box h2 { text-align: center; font-size: 26px; font-weight: 700; margin-bottom: 8px; }
        .login-box p.subtitle { text-align: center; color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 35px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; color: rgba(255,255,255,0.7); }
        .form-group input {
            width: 100%; padding: 14px 18px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px; color: white; font-size: 15px; outline: none;
        }
        .form-group input:focus {
            border-color: #a78bfa;
            background: rgba(167,139,250,0.1);
            box-shadow: 0 0 0 3px rgba(167,139,250,0.2);
        }
        .erreur {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.4);
            color: #fca5a5;
            padding: 12px 16px; border-radius: 10px;
            margin-bottom: 20px; font-size: 14px; text-align: center;
        }
        .btn-login {
            width: 100%; padding: 15px;
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
            border: none; border-radius: 12px;
            color: white; font-size: 16px; font-weight: 600;
            cursor: pointer; margin-top: 10px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(124,58,237,0.5); }
        .footer-text { text-align: center; margin-top: 25px; color: rgba(255,255,255,0.3); font-size: 13px; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="logo">🎥</div>
    <h2>CinemaBD</h2>
    <p class="subtitle">Connectez-vous pour accéder au catalogue</p>

    <?php if ($erreur != ""): ?>
        <div class="erreur">⚠️ <?= $erreur ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label>Login</label>
            <input type="text" name="login" placeholder="Entrez votre login">
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" placeholder="Entrez votre mot de passe">
        </div>
        <button type="submit" class="btn-login">Se connecter →</button>
    </form>
    <p class="footer-text">🔒 Accès réservé aux utilisateurs autorisés</p>
</div>
</body>
</html>
