<?php

namespace Application\ControllerProvider\MembersArea;

use Silex\Application;
use Silex\ControllerProviderInterface;

class EntriesControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match(
            '',
            'Application\Controller\MembersArea\EntriesController::indexAction'
        )
        ->bind('members-area.entries');

        $controllers->match(
            '/new',
            'Application\Controller\MembersArea\EntriesController::newAction'
        )
        ->bind('members-area.entries.new');

        $controllers->match(
            '/{id}/edit',
            'Application\Controller\MembersArea\EntriesController::editAction'
        )
        ->bind('members-area.entries.edit');

        $controllers->match(
            '/{id}/remove',
            'Application\Controller\MembersArea\EntriesController::removeAction'
        )
        ->bind('members-area.entries.remove');

        return $controllers;
    }
}
