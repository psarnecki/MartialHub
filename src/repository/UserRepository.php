<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UserRepository extends Repository {

    public function getUsers(): array {
        $result = [];
        $query = $this->database->connect()->prepare('
            SELECT u.id, u.email, u.role, ud.firstname, ud.lastname 
            FROM users u 
            LEFT JOIN user_details ud ON u.id = ud.user_id 
            ORDER BY u.id ASC
        ');
        $query->execute();
        $users = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $result[] = new User(
                $user['email'],
                '', // Password is not fetched
                $user['firstname'] ?? '',
                $user['lastname'] ?? '',
                $user['role'],
                $user['id']
            );
        }
        return $result;
    }

    public function getUserById(int $id): ?User {
        $query = $this->database->connect()->prepare('
            SELECT u.*, ud.firstname, ud.lastname 
            FROM users u 
            LEFT JOIN user_details ud ON u.id = ud.user_id 
            WHERE u.id = :id
        ');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
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

    public function updateUser(int $id, array $data): bool {
        $db = $this->database->connect();
        
        try {
            $db->beginTransaction();

            $query = $db->prepare('
                UPDATE users SET role = :role WHERE id = :id
            ');
            $query->execute([
                'role' => $data['role'],
                'id' => $id
            ]);

            $query = $db->prepare('
                UPDATE user_details 
                SET firstname = :firstname, lastname = :lastname 
                WHERE user_id = :id
            ');
            $query->execute([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'id' => $id
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public function deleteUser(int $id): bool {
        $query = $this->database->connect()->prepare('DELETE FROM users WHERE id = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }
}