<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UserRepository extends Repository {

    public function getUserByEmail(string $email): ?User {
        $query = $this->database->connect()->prepare('
            SELECT u.*, ud.firstname, ud.lastname 
            FROM users u 
            LEFT JOIN user_details ud ON u.id = ud.user_id 
            WHERE u.email = :email
        ');
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        $userRow = $query->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            return null;
        }

        return new User(
            $userRow['email'],
            $userRow['password'],
            $userRow['firstname'],
            $userRow['lastname'],
            $userRow['role'],
            $userRow['id']
        );
    }

     public function addUser(User $user): void {
        $db = $this->database->connect();
        
        try {
            $db->beginTransaction();

            $query = $db->prepare('
                INSERT INTO users (email, password, role)
                VALUES (?, ?, ?) RETURNING id
            ');
            $query->execute([
                $user->getEmail(),
                $user->getPassword(),
                'user'
            ]);
            
            $userId = $query->fetch(PDO::FETCH_ASSOC)['id'];

            $query = $db->prepare('
                INSERT INTO user_details (user_id, firstname, lastname)
                VALUES (?, ?, ?)
            ');
            $query->execute([
                $userId,
                $user->getFirstName(),
                $user->getLastName()
            ]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}