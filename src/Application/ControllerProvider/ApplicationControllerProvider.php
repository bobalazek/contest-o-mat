<?php

namespace Application\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class ApplicationControllerProvider
    implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '/',
            'Application\Controller\ApplicationController::indexAction'
        )
        ->bind('application');

        $controllers->match(
            '/participate',
            'Application\Controller\ApplicationController::participateAction'
        )
        ->bind('application.participate');

        $controllers->match(
            '/terms',
            'Application\Controller\ApplicationController::termsAction'
        )
        ->bind('application.terms');

        return $controllers;
    }
}
