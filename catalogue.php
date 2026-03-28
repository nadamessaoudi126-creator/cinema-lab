<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

require_once 'connexion.php';
require_once 'Film.php';
require_once 'Auth.php';

$pdo  = getConnexion();
$film = new Film('', '', 0, '', 0.0);

$erreurAcces = '';
if (isset($_SESSION['erreur_acces'])) {
    $erreurAcces = $_SESSION['erreur_acces'];
    unset($_SESSION['erreur_acces']);
}

if (isset($_GET['par_page'])) {
    $parPage = (int)$_GET['par_page'];
    setcookie('film_par_page', $parPage, time() + 30 * 24 * 3600);
} else {
    $parPage = isset($_COOKIE['film_par_page']) ? (int)$_COOKIE['film_par_page'] : 10;
}

$stats = $film->getStats($pdo);
$genreFiltre = trim($_GET['genre'] ?? '');
$films = $genreFiltre !== '' ? Film::getByGenre($pdo, $genreFiltre) : $film->getAll($pdo);
if ($parPage > 0) {
    $films = array_slice($films, 0, $parPage);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue – CinemaBD</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            font-family: 'Segoe UI', sans-serif;
            color: white;
        }
        .navbar {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .brand { font-size: 22px; font-weight: 700; }
        .navbar .brand span { color: #a78bfa; }
        .navbar .user-info { display: flex; align-items: center; gap: 20px; font-size: 14px; color: rgba(255,255,255,0.7); }
        .navbar .user-info strong { color: white; }
        .role-badge {
            background: rgba(167,139,250,0.2);
            border: 1px solid rgba(167,139,250,0.4);
            color: #a78bfa;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .role-badge.visiteur {
            background: rgba(34,197,94,0.2);
            border-color: rgba(34,197,94,0.4);
            color: #4ade80;
        }
        .btn-logout {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.4);
            color: #fca5a5;
            padding: 7px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.3); }
        .main { padding: 30px; max-width: 1200px; margin: 0 auto; }
        .stats-bar {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            gap: 30px;
            font-size: 14px;
            color: rgba(255,255,255,0.7);
        }
        .stats-bar strong { color: #a78bfa; }
        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .controls input[type="text"] {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            outline: none;
            width: 220px;
        }
        .controls input[type="text"]::placeholder { color: rgba(255,255,255,0.3); }
        .controls input[type="text"]:focus { border-color: #a78bfa; box-shadow: 0 0 0 3px rgba(167,139,250,0.2); }
        .controls select {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            outline: none;
            cursor: pointer;
        }
        .controls select option { background: #302b63; }
        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-primary { background: linear-gradient(135deg, #a78bfa, #7c3aed); color: white; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(124,58,237,0.4); }
        .btn-secondary { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.7); }
        .btn-secondary:hover { background: rgba(255,255,255,0.15); }
        .btn-add { background: linear-gradient(135deg, #4ade80, #16a34a); color: white; }
        .btn-add:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(22,163,74,0.4); }
        .erreur-acces {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.4);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .table-wrapper {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: rgba(167,139,250,0.15); }
        thead th {
            padding: 15px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #a78bfa;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tbody tr { border-top: 1px solid rgba(255,255,255,0.06); transition: background 0.2s; }
        tbody tr:hover { background: rgba(255,255,255,0.04); }
        tbody td { padding: 14px 20px; font-size: 14px; color: rgba(255,255,255,0.85); }
        .note-badge {
            background: rgba(167,139,250,0.2);
            color: #a78bfa;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
        }
        .action-links { display: flex; gap: 10px; }
        .btn-edit {
            background: rgba(251,191,36,0.15);
            border: 1px solid rgba(251,191,36,0.3);
            color: #fbbf24;
            padding: 5px 12px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.2s;
        }
        .btn-edit:hover { background: rgba(251,191,36,0.3); }
        .btn-delete {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            padding: 5px 12px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.2s;
        }
        .btn-delete:hover { background: rgba(239,68,68,0.3); }
        .empty-msg { text-align: center; padding: 50px; color: rgba(255,255,255,0.3); font-size: 16px; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="brand">🎥 <span>Cinema</span>BD</div>
    <div class="user-info">
        <?php if (isset($_COOKIE['derniere_connexion'])): ?>
            <span>🕒 <?= htmlspecialchars($_COOKIE['derniere_connexion']) ?></span>
        <?php endif; ?>
        <span>👤 <strong><?= htmlspecialchars($_SESSION['utilisateur']) ?></strong></span>
        <span class="role-badge <?= ($_SESSION['role'] ?? '') === 'visiteur' ? 'visiteur' : '' ?>">
            <?= htmlspecialchars($_SESSION['role'] ?? 'visiteur') ?>
        </span>
        <a href="logout.php" class="btn-logout">Se déconnecter</a>
    </div>
</div>

<div class="main">
    <h1 style="font-size:28px; margin-bottom:20px;">Catalogue de films</h1>

    <?php if ($erreurAcces): ?>
        <div class="erreur-acces">⚠️ <?= htmlspecialchars($erreurAcces) ?></div>
    <?php endif; ?>

    <div class="stats-bar">
        <span>🎬 Total : <strong><?= $stats['total'] ?> films</strong></span>
        <span>⭐ Note moyenne : <strong><?= $stats['note_moyenne'] ?></strong></span>
        <span>🏆 Meilleur film : <strong><?= htmlspecialchars($stats['meilleur_film'] ?? '–') ?></strong></span>
    </div>

    <div class="controls">
        <form method="GET" style="display:flex; gap:10px; align-items:center;">
            <input type="text" name="genre" value="<?= htmlspecialchars($genreFiltre) ?>" placeholder="Filtrer par genre...">
            <button type="submit" class="btn btn-primary">Filtrer</button>
            <?php if ($genreFiltre): ?>
                <a href="catalogue.php" class="btn btn-secondary">Réinitialiser</a>
            <?php endif; ?>
        </form>

        <form method="GET" style="display:flex; gap:10px; align-items:center;">
            <label style="font-size:14px; color:rgba(255,255,255,0.6);">Films par page :</label>
            <select name="par_page" onchange="this.form.submit()">
                <option value="5"  <?= $parPage == 5  ? 'selected' : '' ?>>5</option>
                <option value="10" <?= $parPage == 10 ? 'selected' : '' ?>>10</option>
                <option value="0"  <?= $parPage == 0  ? 'selected' : '' ?>>Tous</option>
            </select>
        </form>

        <?php if (Auth::isAdmin()): ?>
            <a href="ajout.php" class="btn btn-add">+ Ajouter un film</a>
        <?php endif; ?>
    </div>

    <div class="table-wrapper">
        <?php if (empty($films)): ?>
            <div class="empty-msg">🎞️ Aucun film trouvé.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Réalisateur</th>
                    <th>Année</th>
                    <th>Genre</th>
                    <th>Note</th>
                    <?php if (Auth::isAdmin()): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($films as $f): ?>
                <tr>
                    <td><?= htmlspecialchars($f->getTitre()) ?></td>
                    <td><?= htmlspecialchars($f->getRealisateur()) ?></td>
                    <td><?= $f->getAnnee() ?></td>
                    <td><?= htmlspecialchars($f->getGenre()) ?></td>
                    <td><span class="note-badge"><?= $f->getNote() ?></span></td>
                    <?php if (Auth::isAdmin()): ?>
                    <td>
                        <div class="action-links">
                            <a href="modifierFilm.php?id=<?= $f->getId() ?>" class="btn-edit">✏️ Modifier</a>
                            <a href="supprimerFilm.php?id=<?= $f->getId() ?>"
                               onclick="return confirm('Supprimer ce film ?')" class="btn-delete">🗑️ Supprimer</a>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>