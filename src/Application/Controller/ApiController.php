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
