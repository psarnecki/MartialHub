<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/ProfileRepository.php';

class ProfileController extends AppController {

    private $profileRepository;

    public function __construct() {
        $this->profileRepository = new ProfileRepository();
    }

    public function profile() {
        $userId = 3; // Dla testów
        
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