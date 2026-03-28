<?php
session_start();
require_once 'Auth.php';
Auth::requireLogin();
Auth::requireRole('admin');

require_once 'connexion.php';
require_once 'Film.php';

$pdo  = getConnexion();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$film = new Film('', '', 0, '', 0.0);

if ($id > 0 && $film->delete($pdo, $id)) {
    $message = '<p style="color:green">Film supprimé avec succès.</p>';
} else {
    $message = '<p style="color:red">Erreur : film introuvable ou suppression impossible.</p>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Suppression</title></head>
<body>
<?= $message ?>
<a href="catalogue.php">← Retour au catalogue</a>
</body>
</html>
