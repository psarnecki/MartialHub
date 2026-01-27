<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/RankingRepository.php';

class RankingController extends AppController {
    private $rankingRepository;

    public function __construct() {
        $this->rankingRepository = new RankingRepository();
    }

    public function rankings() {
        $ranking = $this->rankingRepository->getRanking('MMA');
        return $this->render('rankings', ['ranking' => $ranking]);
    }
}