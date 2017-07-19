<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = [];

        return $app->redirect(
            $app['url_generator']->generate('application')
        );
    }

    public function contactAction(Request $request, Application $app)
    {
        $data = [];

        $formData = [];
        $form = $app['form.factory']->create(
            \Application\Form\Type\ContactType::class
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formData = $form->getData();

                $message = \Swift_Message::newInstance()
                    ->setSubject(
                        $app['name'].' - '.$app['translator']->trans('Contact')
                    )
                    ->setFrom(
                        [
                            $formData['email'] => $formData['name'],
                        ]
                    )
                    ->setTo(
                        [
                            $app['email'] => $app['emailName'],
                        ]
                    )
                    ->setCc(
                        [
                            $formData['email'] => $formData['name'],
                        ]
                    )
                    ->setBody(
                        $app['mailer.css_to_inline_styles_converter'](
                            'emails/contact.html.twig',
                            [
                                'app' => $app,
                                'formData' => $formData,
                            ]
                        ),
                        'text/html'
                    )
                ;
                $app['mailer']->send($message);

                $data['success'] = true;
                $data['successMessage'] = $app['translator']->trans(
                    'Thanks for your message. We will respond as soon as possible!'
                );
            }
        }

        $data['formData'] = $formData;
        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/contact.html.twig',
                $data
            )
        );
    }
}
