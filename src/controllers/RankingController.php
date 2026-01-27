<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/RankingRepository.php';

class RankingController extends AppController {
    private $rankingRepository;

    public function __construct() {
        $this->rankingRepository = new RankingRepository();
    }

    public function rankings() {
        return $this->render('rankings', [
            'ranking' => $this->rankingRepository->getRanking('MMA', 'individual'),
            'disciplines' => $this->rankingRepository->getDisciplines()
        ]);
    }

    public function filterRanking() {
        $content = json_decode(file_get_contents("php://input"), true);
        header('Content-Type: application/json');
        echo json_encode($this->rankingRepository->getRanking($content['discipline'], $content['type']));
        exit;
    }
}