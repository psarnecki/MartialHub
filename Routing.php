<?php

require_once 'src/controllers/AdminController.php';
require_once 'src/controllers/EventController.php';
require_once 'src/controllers/ProfileController.php';
require_once 'src/controllers/RankingController.php';
require_once 'src/controllers/SecurityController.php';

class Routing {

    private static ?Routing $instance = null;

    private function __construct() {}

    public static function getInstance(): Routing {
        if (self::$instance === null) {
            self::$instance = new Routing();
        }
        return self::$instance;
    }

    public static $routes = [
        "login" => [
            "controller" => "SecurityController",
            "action" => "login"
        ],
        "register" => [
            "controller" => "SecurityController",
            "action" => "register"
        ],
        "index" => [
            "controller" => "EventController",
            "action" => "index"
        ],
        "filterEvents" => [
            "controller" => "EventController",
            "action" => "filter"
        ],
        "events" => [
            "controller" => "EventController",
            "action" => "events"
        ],
        "eventDetails" => [
            "controller" => "EventController",
            "action" => "eventDetails"
        ],
        "profile" => [
            "controller" => "ProfileController",
            "action" => "profile"
        ],
        "rankings" => [
            "controller" => "RankingController",
            "action" => "rankings"
        ],
        "adminUsers" => [
            "controller" => "AdminController",
            "action" => "users"
        ]
    ];

    public function run($path) {

        if (empty($path)) {
            $path = 'index';
        }

        if (preg_match('/^events\/(\d+)$/', $path, $matches)) {
            $controller = Routing::$routes["eventDetails"]["controller"];
            $action = Routing::$routes["eventDetails"]["action"];

            $controllerObj = new $controller;
            $controllerObj->$action((int)$matches[1]);
            return;
        }

        switch($path) {
            case 'login':
            case 'register':
            case 'index':
            case 'events':
            case 'filterEvents':
            case 'eventDetails':
            case 'profile':
            case 'rankings':
            case 'adminUsers':
                $controller = Routing::$routes[$path]['controller'];
                $action = Routing::$routes[$path]['action'];

                $controllerObj = new $controller;
                $controllerObj->$action(null);
                break;
            default:
                include 'public/views/404.html';
                break;
        }
    }
}