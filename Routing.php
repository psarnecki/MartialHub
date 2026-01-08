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
        "logout" => [
            "controller" => "SecurityController",
            "action" => "logout"
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
        "filterProfile" => [
            "controller" => "ProfileController", 
            "action" => "filterProfile"
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

        if (preg_match('/^profile\/(\d+)$/', $path, $matches)) {
            $controller = Routing::$routes["profile"]["controller"];
            $action = Routing::$routes["profile"]["action"];
            
            $controllerObj = new $controller;
            $controllerObj->$action((int)$matches[1]);
            return;
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
            case 'logout':
            case 'register':
            case 'index':
            case 'events':
            case 'filterEvents':
            case 'eventDetails':
            case 'profile':
            case 'filterProfile':
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