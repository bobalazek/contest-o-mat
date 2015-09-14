<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        return new Response(
            $app['twig']->render(
                'contents/application/index.html.twig',
                $data
            )
        );
    }

    public function participateAction(Request $request, Application $app)
    {
        $data = array();

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ParticipateType($app)
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                if(isset($data['participant']) &&
                    is_a($data['participant'], 'Application\Entity\ParticipantEntity'))
                {
                    $participantEntity = $data['participant'];
                    $participantEntity
                        ->setVia('application')
                        ->setIp($app['request']->getClientIp())
                        ->setUserAgent($app['request']->headers->get('User-Agent'))
                    ;

                    $metas = $participantEntity->getMetas();
                    if(! empty($metas)) {
                        foreach($metas as $metaKey => $metaValue) {
                            $metaEntity = new \Application\Entity\ParticipantMetaEntity();

                            $metaEntity
                                ->setKey($metaKey)
                                ->setValue($metaValue)
                            ;

                            $participantEntity
                                ->addParticipantMeta($metaEntity)
                            ;
                        }
                    }

                    $app['orm.em']->persist($participantEntity);
                    $app['orm.em']->flush();
                }

                // Do something with the data!
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/application/participate.html.twig',
                $data
            )
        );
    }

    public function termsAction(Request $request, Application $app)
    {
        $data = array();

        return new Response(
            $app['twig']->render(
                'contents/application/terms.html.twig',
                $data
            )
        );
    }
}
