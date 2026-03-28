<?php
session_start();
require_once 'Auth.php';
Auth::requireLogin();
Auth::requireRole('admin');

require_once 'connexion.php';
require_once 'Film.php';

$pdo     = getConnexion();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film = new Film(
        trim($_POST['titre']),
        trim($_POST['realisateur']),
        (int)$_POST['annee'],
        trim($_POST['genre']),
        (float)$_POST['note']
    );
    if ($film->save($pdo)) {
        $message = '<p style="color:green">Film ajouté avec succès !</p>';
    } else {
        $message = '<p style="color:red">Erreur lors de l\'ajout.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Ajouter un film</title></head>
<body>
<h1>Ajouter un film</h1>
<?= $message ?>
<form method="POST">
    <label>Titre : <input type="text" name="titre" required></label><br><br>
    <label>Réalisateur : <input type="text" name="realisateur" required></label><br><br>
    <label>Année : <input type="number" name="annee" min="1888" max="2099" required></label><br><br>
    <label>Genre : <input type="text" name="genre" required></label><br><br>
    <label>Note : <input type="number" name="note" step="0.1" min="0" max="10" required></label><br><br>
    <button type="submit">Ajouter</button>
</form>
<a href="catalogue.php">← Retour au catalogue</a>
</body>
</html>
