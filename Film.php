<?php
class Film {

    // -- Attributs
    private int $id;
    private string $titre;
    private string $realisateur;
    private int $annee;
    private string $genre;
    private float $note;

    // -- Constructeur
    public function __construct(
        string $titre,
        string $realisateur,
        int $annee,
        string $genre,
        float $note,
        int $id = 0
    ) {
        $this->titre       = $titre;
        $this->realisateur = $realisateur;
        $this->annee       = $annee;
        $this->genre       = $genre;
        $this->note        = $note;
        $this->id          = $id;
    }

    // -- Accesseurs
    public function getId(): int             { return $this->id; }
    public function getTitre(): string       { return $this->titre; }
    public function getRealisateur(): string { return $this->realisateur; }
    public function getAnnee(): int          { return $this->annee; }
    public function getGenre(): string       { return $this->genre; }
    public function getNote(): float         { return $this->note; }

    // -- Modificateurs
    public function setTitre(string $titre): void             { $this->titre = $titre; }
    public function setRealisateur(string $realisateur): void { $this->realisateur = $realisateur; }
    public function setAnnee(int $annee): void                { $this->annee = $annee; }
    public function setGenre(string $genre): void             { $this->genre = $genre; }
    public function setNote(float $note): void                { $this->note = $note; }

    // -- Insert
    public function save(PDO $pdo): bool {
        $sql = "INSERT INTO films (titre, realisateur, annee, genre, note)
                VALUES (:titre, :realisateur, :annee, :genre, :note)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titre',       $this->titre,       PDO::PARAM_STR);
        $stmt->bindValue(':realisateur', $this->realisateur, PDO::PARAM_STR);
        $stmt->bindValue(':annee',       $this->annee,       PDO::PARAM_INT);
        $stmt->bindValue(':genre',       $this->genre,       PDO::PARAM_STR);
        $stmt->bindValue(':note',        $this->note,        PDO::PARAM_STR);
        return $stmt->execute();
    }

    // -- Read all
    public function getAll(PDO $pdo): array {
        $stmt  = $pdo->query("SELECT * FROM films ORDER BY note DESC");
        $films = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $films[] = new Film(
                $row['titre'], $row['realisateur'],
                (int)$row['annee'], $row['genre'],
                (float)$row['note'], (int)$row['id']
            );
        }
        return $films;
    }

    // -- Find by ID
    public function findById(PDO $pdo, int $id): ?Film {
        $stmt = $pdo->prepare("SELECT * FROM films WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Film(
            $row['titre'], $row['realisateur'],
            (int)$row['annee'], $row['genre'],
            (float)$row['note'], (int)$row['id']
        );
    }

    // -- Update
    public function update(PDO $pdo): bool {
        $sql = "UPDATE films SET titre=:titre, realisateur=:realisateur,
                annee=:annee, genre=:genre, note=:note WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titre',       $this->titre,       PDO::PARAM_STR);
        $stmt->bindValue(':realisateur', $this->realisateur, PDO::PARAM_STR);
        $stmt->bindValue(':annee',       $this->annee,       PDO::PARAM_INT);
        $stmt->bindValue(':genre',       $this->genre,       PDO::PARAM_STR);
        $stmt->bindValue(':note',        $this->note,        PDO::PARAM_STR);
        $stmt->bindValue(':id',          $this->id,          PDO::PARAM_INT);
        return $stmt->execute();
    }

    // -- Delete
    public function delete(PDO $pdo, int $id): bool {
        $stmt = $pdo->prepare("DELETE FROM films WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // -- Filter by genre
    public static function getByGenre(PDO $pdo, string $genre): array {
        $stmt = $pdo->prepare("SELECT * FROM films WHERE genre = :genre ORDER BY note DESC");
        $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
        $stmt->execute();
        $films = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $films[] = new Film(
                $row['titre'], $row['realisateur'],
                (int)$row['annee'], $row['genre'],
                (float)$row['note'], (int)$row['id']
            );
        }
        return $films;
    }

    // -- Stats
    public function getStats(PDO $pdo): array {
        $sql = "SELECT COUNT(*) AS total, ROUND(AVG(note),1) AS note_moyenne,
                (SELECT titre FROM films ORDER BY note DESC LIMIT 1) AS meilleur_film
                FROM films";
        $stmt = $pdo->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'total'         => (int)$row['total'],
            'note_moyenne'  => (float)$row['note_moyenne'],
            'meilleur_film' => $row['meilleur_film'],
        ];
    }
}
