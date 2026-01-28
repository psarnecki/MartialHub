<?php

require_once 'AppController.php';

class AdminController extends AppController {

    public function __construct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: /login");
            exit();
        }
    }

    public function users() {
        $userRepository = new UserRepository();
        $users = $userRepository->getUsers();

        return $this->render('admin-users', ['users' => $users]);
    }

    public function editUser(int $id) {
        $userRepository = new UserRepository();

        if ($this->isPost()) {
            $userRepository->updateUser($id, [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'role' => $_POST['role']
            ]);
            header("Location: /adminUsers");
            exit;
        }

        $user = $userRepository->getUserById($id);
        
        if (!$user) {
            header("Location: /adminUsers");
            exit;
        }

        return $this->render('edit-user', ['user' => $user]);
    }

    public function deleteUser(int $id) {
        $currentAdminId = $_SESSION['user_id'] ?? null;

        if ($id === (int)$currentAdminId) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete yourself']);
            exit;
        }

        $userRepository = new UserRepository();
        $success = $userRepository->deleteUser($id);

        if ($success) {
            http_response_code(200);
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error']);
        }
        exit;
    }
}