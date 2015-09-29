<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class WinnersControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\WinnersController::indexAction'
        )
        ->bind('members-area.winners');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\WinnersController::newAction'
        )
        ->bind('members-area.winners.new');

        $controllers->match(
            '/export',
            'Application\Controller\MembersArea\WinnersController::exportAction'
        )
        ->bind('members-area.winners.export');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\WinnersController::editAction'
        )
        ->bind('members-area.winners.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\WinnersController::removeAction'
        )
        ->bind('members-area.winners.remove');

        return $controllers;
    }
}
