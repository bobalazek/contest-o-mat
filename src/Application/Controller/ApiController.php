<?php

namespace Application\Controller;

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

    /********** Participants **********/
    public function participantsPostAction(Request $request, Application $app)
    {
        $requestData = $request->request->all();
        $participantEntity = new \Application\Entity\ParticipantEntity();

        // To-Do: Hydrate the participantEntity with request data

        $validatorErrors = $app['validator']->validate(
            $participantEntity
        );

        if (count($validatorErrors) > 0) {
            $errors = array();

            foreach($validatorErrors as $error) {
                $errorKey = $error->getPropertyPath();
                $errorValue = $error->getMessage();

                $errors[$errorKey] = $errorValue;
            }

            return $app->json(array(
                'status' => 'error',
                'errors' => $errors,
            ));
        }

        $app['orm.em']->persist($participantEntity);
        $app['orm.em']->flush();

        return $app->json(array(
            'status' => 'ok',
        ));
    }

    /********** Entries **********/
    public function entriesPostAction(Request $request, Application $app)
    {
        return $app->json(array(
            'status' => 'ok',
        ));
    }
}
