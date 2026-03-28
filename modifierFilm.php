<?php
session_start();
require_once 'Auth.php';
Auth::requireLogin();
Auth::requireRole('admin');

require_once 'connexion.php';
require_once 'Film.php';

$pdo     = getConnexion();
$id      = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
$message = '';
$filmObj = (new Film('', '', 0, '', 0.0))->findById($pdo, $id);

if ($filmObj === null) {
    echo '<p style="color:red">Film introuvable.</p>';
    echo '<a href="catalogue.php">← Retour</a>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filmObj->setTitre(trim($_POST['titre']));
    $filmObj->setRealisateur(trim($_POST['realisateur']));
    $filmObj->setAnnee((int)$_POST['annee']);
    $filmObj->setGenre(trim($_POST['genre']));
    $filmObj->setNote((float)$_POST['note']);

    if ($filmObj->update($pdo)) {
        header('Location: catalogue.php');
        exit;
    } else {
        $message = '<p style="color:red">Erreur lors de la mise à jour.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Modifier un film</title></head>
<body>
<h1>Modifier le film</h1>
<?= $message ?>
<form method="POST">
    <input type="hidden" name="id" value="<?= $filmObj->getId() ?>">
    <label>Titre : <input type="text" name="titre" value="<?= htmlspecialchars($filmObj->getTitre()) ?>" required></label><br><br>
    <label>Réalisateur : <input type="text" name="realisateur" value="<?= htmlspecialchars($filmObj->getRealisateur()) ?>" required></label><br><br>
    <label>Année : <input type="number" name="annee" value="<?= $filmObj->getAnnee() ?>" min="1888" max="2099" required></label><br><br>
    <label>Genre : <input type="text" name="genre" value="<?= htmlspecialchars($filmObj->getGenre()) ?>" required></label><br><br>
    <label>Note : <input type="number" name="note" step="0.1" min="0" max="10" value="<?= $filmObj->getNote() ?>" required></label><br><br>
    <button type="submit">Enregistrer</button>
</form>
<a href="catalogue.php">← Retour au catalogue</a>
</body>
</html>
