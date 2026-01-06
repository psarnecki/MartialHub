<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/UserProfile.php';

class ProfileRepository extends Repository {

    public function getUserProfile(int $userId): ?UserProfile {
        $query = $this->database->connect()->prepare('
            SELECT ud.*, u.role, c.name as club_name 
            FROM user_details ud
            JOIN users u ON ud.user_id = u.id
            LEFT JOIN clubs c ON ud.club_id = c.id
            WHERE ud.user_id = :id
        ');
        $query->bindParam(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return new UserProfile(
            $data['firstname'], 
            $data['lastname'], 
            $data['role'], 
            $data['club_name'],
            $data['wins'], 
            $data['losses'], 
            $data['draws'],
            $data['bio'], 
            $data['image_url']
        );
    }

    public function getUserHistory(int $userId): array {
        $query = $this->database->connect()->prepare('
            SELECT * FROM v_user_fights WHERE user_id = :id ORDER BY fight_date DESC
        ');
        $query->bindParam(':id', $userId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}