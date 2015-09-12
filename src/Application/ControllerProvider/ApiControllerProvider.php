<?php

namespace Application\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class ApiControllerProvider
    implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '/',
            'Application\Controller\ApiController::indexAction'
        )
        ->bind('api');

        /***** Participants *****/
        $controllers->post(
            '/participants',
            'Application\Controller\ApiController::participantsPostAction'
        )
        ->bind('api.participants');

        /***** Entries *****/
        $controllers->post(
            '/entries',
            'Application\Controller\ApiController::entriesPostAction'
        )
        ->bind('api.entries');

        return $controllers;
    }
}
