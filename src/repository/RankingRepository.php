<?php

require_once 'Repository.php';

class RankingRepository extends Repository {

    public function getRanking(string $discipline, string $type): array {
        $view = ($type === 'club') ? 'v_club_rankings' : 'v_rankings';
        $query = $this->database->connect()->prepare("SELECT * FROM $view WHERE UPPER(discipline) = :disc");
        $query->execute([':disc' => strtoupper($discipline)]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDisciplines(): array {
        $query = $this->database->connect()->prepare('SELECT DISTINCT UPPER(discipline) FROM events ORDER BY 1');
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
}