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
}