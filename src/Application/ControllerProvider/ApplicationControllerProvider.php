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
            '/facebook-authenticate',
            'Application\Controller\ApplicationController::facebookAuthenticateAction'
        )
        ->bind('application.facebook-authenticate');

        $controllers->match(
            '/facebook-authenticated',
            'Application\Controller\ApplicationController::facebookAuthenticatedAction'
        )
        ->bind('application.facebook-authenticated');

        $controllers->match(
            '/entries',
            'Application\Controller\ApplicationController::entriesAction'
        )
        ->bind('application.entries');

        $controllers->match(
            '/entries/{id}',
            'Application\Controller\ApplicationController::entriesDetailAction'
        )
        ->bind('application.entries.detail');

        $controllers->match(
            '/winners',
            'Application\Controller\ApplicationController::winnersAction'
        )
        ->bind('application.winners');

        $controllers->match(
            '/terms',
            'Application\Controller\ApplicationController::termsAction'
        )
        ->bind('application.terms');

        return $controllers;
    }
}
