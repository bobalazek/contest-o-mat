<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array(
            'status' => 'ok',
            'status_code' => 200,
            'message' => 'Hello API!',
        );

        return $app->json(
            $data
        );
    }

    public function entriesAction(Request $request, Application $app)
    {
        $data = array();

        $entries = $app['orm.em']
            ->getRepository('Application\Entity\EntryEntity')
            ->findAll()
        ;

        if ($entries) {
            foreach ($entries as $entry) {
                $data[$entry->getId()] = (string) $entry;
            }
        } else {
            $data['error'] = array(
                'message' => 'No entries found.',
            );
        }

        return $app->json(
            $data
        );
    }

    public function participantsAction(Request $request, Application $app)
    {
        $data = array();

        $participants = $app['orm.em']
            ->getRepository('Application\Entity\ParticipantEntity')
            ->findAll()
        ;

        if ($participants) {
            foreach ($participants as $participant) {
                $data[$participant->getId()] = (string) $participant;
            }
        } else {
            $data['error'] = array(
                'message' => 'No participants found.',
            );
        }

        return $app->json(
            $data
        );
    }
}
