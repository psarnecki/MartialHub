<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/ProfileRepository.php';

class ProfileController extends AppController {

    private $profileRepository;

    public function __construct() {
        $this->profileRepository = new ProfileRepository();
    }

    public function profile(?int $id = null) {
        if (!isset($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/login");

            exit;
        }

        if ($id !== null && $id !== (int)$_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
            $this->terminateWithError(403);
        }

        $userId = $id ?: $_SESSION['user_id'];

        $profile = $this->profileRepository->getUserProfile($userId);
        $history = $this->profileRepository->getUserHistory($userId);

        if (!$profile) {
            $this->terminateWithError(404);
        }

        return $this->render('profile', [
            'profile' => $profile,
            'history' => $history
        ]);
    }

    public function filterProfile() {
        $data = $this->getJsonData(['discipline', 'userId']);

        header('Content-Type: application/json');

        $history = $this->profileRepository->getUserHistoryByDiscipline(
            $data['userId'],
            strtoupper($data['discipline'])
        );
        
        echo json_encode($history);
        exit;
    }
}