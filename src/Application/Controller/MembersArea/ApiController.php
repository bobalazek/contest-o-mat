<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = [
            'status' => 'ok',
            'status_code' => 200,
            'message' => 'Hello API!',
        ];

        return $app->json(
            $data
        );
    }

    public function participantsAction(Request $request, Application $app)
    {
        $data = [];

        $query = $request->request->get('q');

        $participants = $app['orm.em']
            ->getRepository('Application\Entity\ParticipantEntity')
            ->getByQuery($query)
        ;

        if ($participants) {
            foreach ($participants as $participant) {
                $data[] = [
                    'value' => $participant->getId(),
                    'text' => (string) $participant,
                ];
            }
        }

        return $app->json(
            $data
        );
    }

    public function entriesAction(Request $request, Application $app)
    {
        $data = [];

        $query = $request->request->get('q');

        $entries = $app['orm.em']
            ->getRepository('Application\Entity\EntryEntity')
            ->getByQuery($query)
        ;

        if ($entries) {
            foreach ($entries as $entry) {
                $data[] = [
                    'value' => $entry->getId(),
                    'text' => (string) $entry,
                ];
            }
        }

        return $app->json(
            $data
        );
    }
}
