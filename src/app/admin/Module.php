<?php

namespace Multi\Admin;

use Phalcon\Loader;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;
use Phalcon\Escaper;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(
        DiInterface $container = null
    ) {
        $loader = new Loader();
        $loader->registerNamespaces(
            [
                'Multi\Admin\Controllers' => APP_PATH . '/admin/controllers/',
                'Multi\Admin\Models'      => '../app/admin/models/',
                'Multi\Admin\Components' => APP_PATH . '/admin/components/'
            ]
        );

        $loader->register();
    }

    public function registerServices(DiInterface $container)
    {
        // Registering a dispatcher
        $container->set(
            'dispatcher',
            function () {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace(
                    'Multi\Admin\Controllers'
                );

                return $dispatcher;
            }
        );

        // Registering the view component
        $container->set(
            'view',
            function () {
                $view = new View();
                $view->setViewsDir(
                    '../app/admin/views/'
                );

                return $view;
            }
        );
        $container->set('escaper', function () {
            $escaper = new Escaper();
            return $escaper;
        });
        $container->set("logger", function () {
            $adapter = new Stream('../app/admin/logs/main.log');
            $logger  = new Logger(
                'messages',
                [
                    'main' => $adapter,
                ]
            );
            return $logger;
        });
    }
}
