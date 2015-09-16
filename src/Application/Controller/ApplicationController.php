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

        $currentDatetime = new \Datetime('now');
        $startDatetime = $app['settings']['startDate']
            ? new \Datetime($app['settings']['startDate'])
            : false
        ;
        $endDatetime = $app['settings']['endDate']
            ? new \Datetime($app['settings']['endDate'])
            : false
        ;

        if ($startDatetime &&
            $startDatetime > $currentDatetime) {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = $app['settings']['texts']['notYetStarted'];
        } elseif ($endDatetime &&
            $endDatetime < $currentDatetime) {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = $app['settings']['texts']['hasEnded'];
        } elseif (! $app['facebookUser'] &&
            $app['settings']['onlyFacebookUsersCanParticipate']) {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = $app['settings']['texts']['onlyFacebookUsersCanParticipate'].
            '<br/><br/><a class="btn btn-block btn-lg btn-primary"
                href="'.$app['url_generator']->generate('application.facebook-authenticate').'">'.
                $app['settings']['texts']['onlyFacebookUsersCanParticipateButton'].
            '</a>';
        } elseif ($app['participant'] &&
            $app['participant']->hasAlreadyParticipatedToday() &&
            $app['settings']['canParticipateOncePerDay']) {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = $app['settings']['texts']['alreadyParticipatedToday'];
        } elseif ($app['participant'] &&
            $app['settings']['canParticipateOnlyOnce']) {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = $app['settings']['texts']['alreadyParticipated'];
        } else {
            $form = $app['form.factory']->create(
                new \Application\Form\Type\Participate\DefaultType($app)
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

                        if ($app['facebookUser']) {
                            $uid = 'facebook:'.$app['facebookUser']->id;

                            $participantEntity
                                ->setUid($uid)
                            ;

                            // Maybe add some other attributes inside the metas?
                            // https://developers.facebook.com/docs/graph-api/reference/user
                            foreach ($app['facebookUser'] as $key => $value) {
                                $participantEntity
                                    ->addMeta(
                                        $key,
                                        $value
                                    )
                                ;
                            }
                        }

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

                        if ($app['settings']['useSameParticipantDataAfterFirstEntry']) {
                            $participantCookie = new \Symfony\Component\HttpFoundation\Cookie(
                                'participant_id',
                                $participantEntity->getId(),
                                time() + (DAY_IN_MINUTES * 365)
                            );
                            $response->headers->setCookie($participantCookie);
                        }
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
                        } catch (\Exception $e) {
                        }
                    }

                    $data['showForm'] = false;
                    $data['alert'] = 'success';
                    $data['alertMessage'] = $app['settings']['texts']['thanksForYourParticipation'];
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

    public function facebookAuthenticateAction(Request $request, Application $app)
    {
        if ($app['facebookSdk']) {
            $redirectLoginHelper = $app['facebookSdk']->getRedirectLoginHelper();
            $url = $app['baseUrl'].str_replace(
                    $app['baseUri'],
                    '',
                    $app['url_generator']->generate('application.facebook-authenticated')
                )
            ;

            $loginUrl = $redirectLoginHelper->getLoginUrl(
                $url,
                $app['facebookSdkOptions']['permissions']
            );

            return $app->redirect(
                $loginUrl
            );
        } else {
            return $app->redirect(
                $app['url_generator']->generate('application')
            );
        }
    }

    public function facebookAuthenticatedAction(Request $request, Application $app)
    {
        if ($app['facebookSdk']) {
            $redirectLoginHelper = $app['facebookSdk']->getRedirectLoginHelper();

            try {
                $accessToken = $redirectLoginHelper->getAccessToken();

                if($accessToken) {
                    $app['session']->set(
                        'fb_access_token',
                        (string) $accessToken
                    );

                    $app['flashbag']->add(
                        'success',
                        $app['translator']->trans(
                            'You have successfully authenticated to Facebook.'
                        )
                    );
                } else {
                    $app['flashbag']->add(
                        'danger',
                        $app['translator']->trans(
                            'Something went wrong. Could not get your access token.'
                        )
                    );
                }
            } catch (\Exception $e) {
                $app['flashbag']->add(
                    'danger',
                    $app['translator']->trans(
                        $e->getMessage()
                    )
                );
            }

            return $app->redirect(
                $app['url_generator']->generate('application.participate')
            );
        } else {
            return $app->redirect(
                $app['url_generator']->generate('application')
            );
        }
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
