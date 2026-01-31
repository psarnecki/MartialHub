<?php

require_once 'Repository.php';
require_once __DIR__.'/../viewmodels/ProfileViewModel.php';

class ProfileRepository extends Repository {

    public function getUserProfile(int $userId): ?ProfileViewModel {
        $query = $this->database->execute('
            SELECT 
                ud.*, u.role, c.name as club_name,
                COALESCE(SUM(ar.wins), 0) as total_wins,
                COALESCE(SUM(ar.losses), 0) as total_losses,
                COALESCE(SUM(ar.draws), 0) as total_draws
            FROM user_details ud
            JOIN users u ON ud.user_id = u.id
            LEFT JOIN clubs c ON ud.club_id = c.id
            LEFT JOIN v_athlete_records ar ON u.id = ar.user_id
            WHERE ud.user_id = :id
            GROUP BY ud.id, u.role, c.name
        ', ['id' => $userId]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return new ProfileViewModel(
            $data['user_id'],
            $data['firstname'], 
            $data['lastname'], 
            $data['role'], 
            $data['club_name'],
            $data['total_wins'],
            $data['total_losses'],
            $data['total_draws'],
            $data['bio'], 
            $data['image_url']
        );
    }

    public function getUserHistory(int $userId): array {
        $query = $this->database->execute('
            SELECT * FROM v_user_fights WHERE user_id = :id ORDER BY fight_date DESC
        ', ['id' => $userId]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserHistoryByDiscipline(int $userId, string $discipline): array {
        $query = $this->database->execute('
            SELECT * FROM v_user_fights 
            WHERE user_id = :id AND UPPER(discipline) = :discipline 
            ORDER BY fight_date DESC
        ', ['id' => $userId, 'discipline' => $discipline]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}