<?php

require_once 'Repository.php';

class RankingRepository extends Repository {

    public function getRanking(string $discipline = 'MMA'): array {
        $query = $this->database->connect()->prepare('
            SELECT * FROM v_rankings WHERE discipline = :disc LIMIT 10
        ');
        $query->bindParam(':disc', $discipline, PDO::PARAM_STR);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}