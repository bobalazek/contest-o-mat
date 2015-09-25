<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Entity\VoteEntity;

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
            $app['settings']['participateInterval'] == 'once_per_day') {
            $data['showForm'] = false;
            $data['alert'] = 'info';
            $data['alertMessage'] = $app['settings']['texts']['alreadyParticipatedToday'];
        } elseif ($app['participant'] &&
            $app['settings']['participateInterval'] == 'only_once') {
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
                            ->setIp($request->getClientIp())
                            ->setUserAgent($request->headers->get('User-Agent'))
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

                        $participantEntity->convertMetasToEntryMetas(
                            $uploadPath = $app['baseUrl'].'/assets/uploads/',
                            $uploadDir = WEB_DIR.'/assets/uploads/'
                        );

                        $app['orm.em']->persist($participantEntity);
                        $app['orm.em']->flush();

                        if ($app['settings']['useSameParticipantDataAfterFirstEntry']) {
                            $participantCookie = new \Symfony\Component\HttpFoundation\Cookie(
                                'participant_data',
                                ($participantEntity->getId().':'.
                                md5((string) $participantEntity).':'.
                                md5($app['baseUrl'])),
                                time() + (DAY_IN_MINUTES * 365),
                                $app['baseUri']
                            );
                            $response->headers->setCookie($participantCookie);
                        }
                    }

                    if (isset($data['entry']) &&
                        is_a($data['entry'], 'Application\Entity\EntryEntity')) {
                        $entryEntity = $data['entry'];
                        $entryEntity
                            ->setIp($request->getClientIp())
                            ->setUserAgent($request->headers->get('User-Agent'))
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

                        $entryEntity->convertMetasToEntryMetas(
                            $uploadPath = $app['baseUrl'].'/assets/uploads/',
                            $uploadDir = WEB_DIR.'/assets/uploads/'
                        );

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
            $redirectUrl = $request->headers->get('referer');

            if ($request->query->has('redirect_url')) {
                $redirectUrl = $request->query->get('redirect_url');
            }

            $redirectLoginHelper = $app['facebookSdk']->getRedirectLoginHelper();

            $url = $app['baseUrl'].str_replace(
                    $app['baseUri'],
                    '',
                    $app['url_generator']->generate('application.facebook-authenticated').
                    ($redirectUrl ? '?redirect_url='.urlencode($redirectUrl) : '')
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
            $app['flashbag']->add(
                'warning',
                $app['translator']->trans(
                    'Facebook SDK is not set up yet.'
                )
            );

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

                if (
                    $request->query->has('via_javascript')
                    && $request->query->has('facebook_access_token')
                    && ! $accessToken
                ) {
                    $facebookAccessToken =  $request->query->get(
                        'facebook_access_token',
                        false
                    );

                    $accessToken = $facebookAccessToken;
                }

                if ($accessToken) {
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

            $redirectUrl = $request->query->get('redirect_url', false);

            if ($redirectUrl) {
                return $app->redirect(urldecode($redirectUrl));
            }

            return $app->redirect(
                $app['url_generator']->generate('application.participate')
            );
        } else {
            $app['flashbag']->add(
                'warning',
                $app['translator']->trans(
                    'Facebook SDK is not set up yet.'
                )
            );

            return $app->redirect(
                $app['url_generator']->generate('application')
            );
        }
    }

    public function entriesAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['settings']['entriesArePublic']) {
            $app->abort(404);
        }

        $limitPerPage = 9;
        $currentPage = $request->query->get('page');

        $entryResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('e')
            ->from('Application\Entity\EntryEntity', 'e')
            ->leftJoin('e.entryMetas', 'em')
            ->leftJoin('e.participant', 'p')
        ;

        $pagination = $app['paginator']->paginate(
            $entryResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'application.entries',
                'defaultSortFieldName' => 'e.timeCreated',
                'defaultSortDirection' => 'desc',
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/application/entries.html.twig',
                $data
            )
        );
    }

    public function entriesDetailAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['settings']['entriesArePublic']) {
            $app->abort(404);
        }

        $entry = $app['orm.em']->find(
            'Application\Entity\EntryEntity',
            $id
        );

        if (! $entry) {
            $app->abort(404);
        }

        $data['entry'] = $entry;

        return new Response(
            $app['twig']->render(
                'contents/application/entries/detail.html.twig',
                $data
            )
        );
    }

    public function entriesVoteAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['settings']['entriesArePublic']) {
            $app->abort(404);
        }

        $entry = $app['orm.em']->find(
            'Application\Entity\EntryEntity',
            $id
        );

        if (! $entry) {
            $app->abort(404);
        }

        $uid = false;

        // Here you shall set the user UID. Normally that is via facebook id,
        // but you can set that however you want. The main thing is, that it's
        // always the same for the same voter.
        if ($app['facebookUser']) {
            $uid = 'facebook:'.$app['facebookUser']->id;
        }

        if ($uid) {
            $currentTime = new \Datetime();
            $alreadyVoted = false;
            $alreadyVotedToday = false;
            $alreadyVotedPerEntry = false;
            $alreadyVotedPerEntryToday = false;

            $lastVoteByUid = $app['orm.em']
                ->getRepository('Application\Entity\VoteEntity')
                ->findOneBy(array(
                    'voterUid' => $uid,
                ), array(
                    'timeCreated' => 'DESC',
                ))
            ;
            $lastVoteByUidPerEntry = $app['orm.em']
                ->getRepository('Application\Entity\VoteEntity')
                ->findOneBy(array(
                    'entry' => $entry,
                    'voterUid' => $uid,
                ), array(
                    'timeCreated' => 'DESC',
                ))
            ;

            if ($lastVoteByUid) {
                // If we found an entry, that means that the user has already participated
                $alreadyVoted = true;

                $lastVoteTime = $lastVoteByUid->getTimeCreated();
                if ($currentTime->format('Y-m-d') == $lastVoteTime->format('Y-m-d')) {
                    $alreadyVotedToday = true;
                }
            }
            if ($lastVoteByUidPerEntry) {
                // If we found an entry, that means that the user has already participated
                $alreadyVotedPerEntry = true;

                $lastVoteByEntryTime = $lastVoteByUidPerEntry->getTimeCreated();
                if ($currentTime->format('Y-m-d') == $lastVoteByEntryTime->format('Y-m-d')) {
                    $alreadyVotedPerEntryToday = true;
                }
            }

            if ($app['settings']['voteInterval'] == 'once_per_day'
                && $alreadyVotedToday) {
                $app['flashbag']->add(
                    'info',
                    $app['settings']['texts']['alreadyVotedToday']
                );
            } elseif ($app['settings']['voteInterval'] == 'only_once'
                && $alreadyVoted) {
                $app['flashbag']->add(
                    'info',
                    $app['settings']['texts']['alreadyVoted']
                );
            } elseif ($app['settings']['voteInterval'] == 'once_per_day_per_entry'
                && $alreadyVotedPerEntryToday) {
                $app['flashbag']->add(
                    'info',
                    $app['settings']['texts']['alreadyVotedForThisEntryToday']
                );
            } elseif ($app['settings']['voteInterval'] == 'only_once_per_entry'
                && $alreadyVotedPerEntry) {
                $app['flashbag']->add(
                    'info',
                    $app['settings']['texts']['alreadyVotedForThisEntry']
                );
            } else {
                $voteEntity = new \Application\Entity\VoteEntity();

                $voteEntity
                    ->setVoterUid($uid)
                    ->setEntry($entry)
                    ->setIp($request->getClientIp())
                    ->setUserAgent($request->headers->get('User-Agent'))
                ;

                if ($app['facebookUser']) {
                    // Maybe add some other attributes inside the metas?
                    // https://developers.facebook.com/docs/graph-api/reference/user
                    foreach ($app['facebookUser'] as $key => $value) {
                        $voteEntity
                            ->addMeta(
                                $key,
                                $value
                            )
                        ;
                    }
                }

                $metas = $voteEntity->getMetas();
                if (! empty($metas)) {
                    foreach ($metas as $metaKey => $metaValue) {
                        $metaEntity = new \Application\Entity\VoteMetaEntity();

                        $metaEntity
                            ->setKey($metaKey)
                            ->setValue($metaValue)
                        ;

                        $voteEntity
                            ->addVoteMeta($metaEntity)
                        ;
                    }
                }

                $app['orm.em']->persist($voteEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'You have successfully voted!'
                    )
                );
            }
        } else {
            $facebookAuthenticateUrl = $app['url_generator']->generate('application.facebook-authenticate');

            $app['flashbag']->add(
                'info',
                $app['translator']->trans(
                    'You must be authorized to vote. Please
                    <a class="btn-facebook-authenticate" href="'.$facebookAuthenticateUrl.'">login first</a>.
                    Thank you.'
                )
            );
        }

        return $app->redirect(
            $app['url_generator']->generate(
                'application.entries.detail',
                array(
                    'id' => $entry->getId(),
                )
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
