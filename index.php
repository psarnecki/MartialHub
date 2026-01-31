<?php

require_once 'Routing.php';
require_once 'Database.php';

session_start();

if (getenv('SEED_DATA') === 'true') {
    try {
        $db = Database::getInstance();
        if ($db->shouldSeed()) {
            $db->seed();
        }
    } catch (Exception $e) {
        error_log("Seeding failed: " . $e->getMessage());
    }
}

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Routing::getInstance()->run($path);