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
        $data = $this->getJsonData(['discipline', 'type']);

        header('Content-Type: application/json');

        $ranking = $this->rankingRepository->getRanking(
            strtoupper($data['discipline']), 
            strtoupper($data['type'])
        );

        echo json_encode($ranking);
        exit;
    }
}