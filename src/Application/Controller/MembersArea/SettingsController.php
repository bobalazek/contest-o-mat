<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = [];

        if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\SettingsType($app)
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    file_put_contents(
                        STORAGE_DIR.'/database/'.$app['settingsFile'],
                        json_encode($data)
                    );

                    $app['flashbag']->add(
                        'success',
                        $app['translator']->trans(
                            'The settings has been saved!'
                        )
                    );
                } catch (\Exception $e) {
                    $app['flashbag']->add(
                        'warning',
                        $app['translator']->trans(
                            'Whops, something went wrong while saving. Could not save the data inside the "'.
                            STORAGE_DIR.'/database/'.$app['settingsFile'].'" file. Please check, if the file has the right premissions (and if it exists).'
                        )
                    );
                }

                // So the new settings will apply in the same request
                return $app->redirect(
                    $app['url_generator']->generate('members-area.settings')
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/settings/index.html.twig',
                $data
            )
        );
    }
}
