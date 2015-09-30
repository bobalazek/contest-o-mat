<?php

namespace Application\ControllerProvider\MembersArea;

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
            'Application\Controller\MembersArea\ApiController::indexAction'
        )
        ->bind('members-area.api');

        $controllers->match(
            '/participants',
            'Application\Controller\MembersArea\ApiController::participantsAction'
        )
        ->bind('members-area.api.participants');

        $controllers->match(
            '/entries',
            'Application\Controller\MembersArea\ApiController::entriesAction'
        )
        ->bind('members-area.api.entries');

        return $controllers;
    }
}
