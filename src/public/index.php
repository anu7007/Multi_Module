<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Session\Manager;
use Phalcon\Http\Response\Cookies;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Config\ConfigFactory;
use Phalcon\Mvc\Router;

require_once('../app/vendor/autoload.php');

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
date_default_timezone_set('Asia/Kolkata');
// Register an autoloader
$loader = new Loader();


$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
$loader->registerNamespaces([
    'App\Components' => APP_PATH . '/components',
]);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    "cookies",
    function () {
        $cookies = new Cookies();
        $cookies->useEncryption(false);
        return $cookies;
    }
);

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);
$container->set(
    'router',
    function () {
        $router = new Router();

        $router->setDefaultModule('admin');

        $router->add(
            '/admin/signup/:action',
            [
                'module'     => 'admin',
                'controller' => 'signup',
                'action'     => 1,
            ]
        );

        $router->add(
            '/admin/login/:action',
            [
                'module'     => 'admin',
                'controller' => 'login',
                'action'     => 1,
            ]
        );
        $router->add(
            '/admin/user/:action',
            [
                'module'     => 'admin',
                'controller' => 'user',
                'action'     => 1,
            ]
        );
        $router->add(
            '/admin/index/:action',
            [
                'module'     => 'admin',
                'controller' => 'index',
                'action'     => 1,
            ]
        );

        $router->add(
            '/admin/order/:action',
            [
                'module'     => 'admin',
                'controller' => 'order',
                'action'     => 1,
            ]
        );
        // $router->add(
        //     '/signup/:action',
        //     [
        //         'module'     => 'front',
        //         'controller' => 'signup',
        //         'action'     => 1,
        //     ]
        // );

        $router->add(
            '/',
            [
                'module'     => 'admin',
                'controller' => 'login',
                'action'     => 'index',
            ]
        );

        // $router->add(
        //     '/products/:action',
        //     [
        //         'controller' => 'products',
        //         'action'     => 1,
        //     ]
        // );

        return $router;
    }
);
$application = new Application($container);
$application->registerModules(
    [
        'front' => [
            'className' => \Multi\Front\Module::class,
            'path'      => APP_PATH . '/front/Module.php',
        ],
        'admin'  => [
            'className' => \Multi\Admin\Module::class,
            'path'      => APP_PATH . '/admin/Module.php',
        ]
    ]
);
$container->set(
    'config',
    function () {
        $fileName = '../app/etc/config.php';
        $factory = new ConfigFactory();
        return $factory->newInstance("php", $fileName);
        // return new Config($configData);
    }
);


// $container->set(
//     'db',
//     function () {
//         return new Mysql(
//             [

//                 'host'     => 'mysql-server',
//                 'username' => 'root',
//                 'password' => 'secret',
//                 'dbname'   => 'spotify',
//                 ]
//             );
//         }
// );

// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('db');
//     },
//     true
// );
$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client("mongodb://mongo", array("username" => 'root', "password" => "password123"));
        // mongo "mongodb+srv://sandbox.g819z.mongodb.net/myFirstDatabase" --username root

        return $mongo->store;
    },
    true
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
