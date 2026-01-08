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

        $userId = $id ?: $_SESSION['user_id'];
        
        $profile = $this->profileRepository->getUserProfile($userId);
        $history = $this->profileRepository->getUserHistory($userId);

        if (!$profile) {
            return $this->render('404');
        }

        return $this->render('profile', [
            'profile' => $profile,
            'history' => $history
        ]);
    }

    public function filterProfile() {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if (strpos($contentType, "application/json") !== false) {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
            
            $discipline = strtoupper($decoded['discipline']);
            $userId = $decoded['userId'];

            header('Content-Type: application/json');
            
            $history = $this->profileRepository->getUserHistoryByDiscipline($userId, $discipline);
            
            echo json_encode($history);

            exit;
        }
    }
}