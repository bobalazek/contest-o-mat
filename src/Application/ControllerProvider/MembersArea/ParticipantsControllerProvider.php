<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class ParticipantsControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\ParticipantsController::indexAction'
        )
        ->bind('members-area.participants');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\ParticipantsController::newAction'
        )
        ->bind('members-area.participants.new');

        $controllers->match(
            '/export',
            'Application\Controller\MembersArea\ParticipantsController::exportAction'
        )
        ->bind('members-area.participants.export');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\ParticipantsController::editAction'
        )
        ->bind('members-area.participants.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\ParticipantsController::removeAction'
        )
        ->bind('members-area.participants.remove');

        return $controllers;
    }
}
