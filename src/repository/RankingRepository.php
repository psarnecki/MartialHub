<?php

require_once 'Repository.php';

class RankingRepository extends Repository {

    public function getRanking(string $discipline, string $type): array {
        $view = ($type === 'club') ? 'v_club_rankings' : 'v_rankings';
        $query = $this->database->execute("SELECT * FROM $view WHERE UPPER(discipline) = :disc", ['disc' => strtoupper($discipline)]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDisciplines(): array {
        $query = $this->database->execute('SELECT DISTINCT UPPER(discipline) FROM events ORDER BY 1');
        
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
}