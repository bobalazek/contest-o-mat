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
        $response = new Response();
        $data = array();

        $data['showForm'] = true;
        $data['alert'] = false;
        $data['alertMessage'] = '';

        if ($app['participant'] &&
            $app['participant']->hasAlreadyParticipatedToday() &&
            $app['settings']['canParticipateOncePerDay']) {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = 'You have already participated today. Come back tomorrow. Thanks!';
        } elseif ($app['participant'] &&
            $app['settings']['canParticipateOnlyOnce']) {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = 'You have already participated. Thanks!';
        } else {
            $form = $app['form.factory']->create(
                new \Application\Form\Type\ParticipateType($app)
            );

            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $data = $form->getData();

                    if (isset($data['participant']) &&
                        is_a($data['participant'], 'Application\Entity\ParticipantEntity')) {
                        $participantEntity = $data['participant'];
                        $participantEntity
                            ->setVia('application')
                            ->setIp($app['request']->getClientIp())
                            ->setUserAgent($app['request']->headers->get('User-Agent'))
                        ;

                        $metas = $participantEntity->getMetas();
                        if (! empty($metas)) {
                            foreach ($metas as $metaKey => $metaValue) {
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

                        $participantCookie = new \Symfony\Component\HttpFoundation\Cookie(
                            'participant_id',
                            $participantEntity->getId(),
                            time() + (DAY_IN_MINUTES * 365)
                        );
                        $response->headers->setCookie($participantCookie);
                    }

                    if (isset($data['entry']) &&
                        is_a($data['entry'], 'Application\Entity\EntryEntity')) {
                        $entryEntity = $data['entry'];
                        $entryEntity
                            ->setIp($app['request']->getClientIp())
                            ->setUserAgent($app['request']->headers->get('User-Agent'))
                        ;

                        $participant = $app['participant']
                            ? $app['participant']
                            : (isset($participantEntity)
                                ? $participantEntity
                                : false)
                        ;

                        if ($participant) {
                            $entryEntity
                                ->setParticipant($participant)
                            ;
                        }

                        $metas = $entryEntity->getMetas();
                        if (! empty($metas)) {
                            foreach ($metas as $metaKey => $metaValue) {
                                $metaEntity = new \Application\Entity\EntryMetaEntity();

                                $metaEntity
                                    ->setKey($metaKey)
                                    ->setValue($metaValue)
                                ;

                                $entryEntity
                                    ->addEntryMeta($metaEntity)
                                ;
                            }
                        }

                        $app['orm.em']->persist($entryEntity);
                        $app['orm.em']->flush();
                    }

                    if ($participant &&
                        $app['settings']['sendEmailToParticipantOnEntry']) {
                        try {
                            $app['application.mailer']
                                ->swiftMessageInitializeAndSend(array(
                                    'subject' => $app['name'].' - '.$app['translator']->trans('Thanks for participating!'),
                                    'to' => array( $participant->getEmail() ),
                                    'body' => 'emails/participants/new-entry.html.twig',
                                    'type' => 'participant.entry.new',
                                    'templateData' => array(
                                        'participant' => $participant,
                                    ),
                                ))
                            ;
                        } catch (\Exception $e) {}
                    }

                    $data['showForm'] = false;
                    $data['alert'] = 'success';
                    $data['alertMessage'] = 'You have successfully participated in our prizegame!';
                }
            }

            $data['form'] = $form->createView();
        }

        return $response->setContent(
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
