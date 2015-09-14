<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        return $app->redirect(
            $app['url_generator']->generate('application')
        );
    }
}
