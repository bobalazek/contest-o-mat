<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class VotesControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\VotesController::indexAction'
        )
        ->bind('members-area.votes');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\VotesController::newAction'
        )
        ->bind('members-area.votes.new');

        $controllers->match(
            '/export',
            'Application\Controller\MembersArea\VotesController::exportAction'
        )
        ->bind('members-area.votes.export');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\VotesController::editAction'
        )
        ->bind('members-area.votes.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\VotesController::removeAction'
        )
        ->bind('members-area.votes.remove');

        return $controllers;
    }
}
