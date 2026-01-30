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
        "eventResults" => [
            "controller" => "EventController",
            "action" => "eventResults"
        ],
        "registerEvent" => [
            "controller" => "EventController",
            "action" => "registerEvent"
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
        "filterRanking" => [
            "controller" => "RankingController",
            "action" => "filterRanking"
        ],
        "adminUsers" => [
            "controller" => "AdminController",
            "action" => "users"
        ],
        "deleteUser" => [
            "controller" => "AdminController",
            "action" => "deleteUser"
        ],
        "editUser" => [
            "controller" => "AdminController",
            "action" => "editUser"
        ]
    ];

    public static $parameterizedRoutes = [
        '/^deleteUser\/(\d+)$/'   => 'deleteUser',
        '/^editUser\/(\d+)$/'     => 'editUser',
        '/^profile\/(\d+)$/'      => 'profile',
        '/^events\/(\d+)$/'       => 'eventDetails',
        '/^eventResults\/(\d+)$/' => 'eventResults',
        '/^registerEvent\/(\d+)$/' => 'registerEvent'
    ];

    public function run($path) {
        if (empty($path)) {
            $path = 'index';
        }

        foreach (Routing::$parameterizedRoutes as $pattern => $routeKey) {
            if (preg_match($pattern, $path, $matches)) {
                $controller = Routing::$routes[$routeKey]["controller"];
                $action = Routing::$routes[$routeKey]["action"];

                $controllerObj = new $controller;
                $controllerObj->$action((int)$matches[1]);
                return;
            }
        }

        if (isset(Routing::$routes[$path])) {
            $controller = Routing::$routes[$path]['controller'];
            $action = Routing::$routes[$path]['action'];

            $controllerObj = new $controller;
            $controllerObj->$action(null);
        } else {
            $errorController = new AppController();
            $errorController->terminateWithError(404);
        }
    }
}