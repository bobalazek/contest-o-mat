<?php

namespace Application\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class IndexControllerProvider
    implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '/',
            'Application\Controller\IndexController::indexAction'
        )
        ->bind('index');

        $controllers->match(
            '/contact',
            'Application\Controller\IndexController::contactAction'
        )
        ->bind('contact');

        return $controllers;
    }
}
