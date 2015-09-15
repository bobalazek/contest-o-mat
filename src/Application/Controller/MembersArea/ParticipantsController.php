<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantsController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_PARTICIPANTS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 10);
        $currentPage = $request->query->get('page');

        $participantResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('p')
            ->from('Application\Entity\ParticipantEntity', 'p')
            ->leftJoin('p.participantMetas', 'pm')
        ;

        $pagination = $app['paginator']->paginate(
            $participantResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.participants',
                'defaultSortFieldName' => 'p.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => array(
                    'p.name',
                    'p.email',
                    'p.via',
                    'p.ip',
                    'p.userAgent',
                    'pm.value',
                ),
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/members-area/participants/index.html.twig',
                $data
            )
        );
    }

    public function exportAction(Request $request, Application $app)
    {
        // WIP
        $response = new StreamedResponse();
        $response->setCallback(function () use ($app) {
            $handle = fopen('php://output', 'w+');

            fputcsv(
                $handle,
                array(
                    'ID',
                    'Name',
                    'Email',
                    'Via',
                    'Time',
                ),
                ';'
            );

            $participants = $app['orm.em']
                ->getRepository('Application\Entity\ParticipantEntity')
                ->findAll()
            ;

            foreach ($participants as $participant) {
                fputcsv(
                    $handle,
                    array(
                        $participant->getId(),
                        $participant->getName(),
                        $participant->getEmail(),
                        $participant->getVia(),
                        $participant->getTimeCreated()->format(DATE_ATOM),
                    ),
                    ';'
                 );
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="participants.csv"');

        return $response;
    }

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_PARTICIPANTS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ParticipantType(),
            new \Application\Entity\ParticipantEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $participantEntity = $form->getData();
                $participantEntity
                    ->setIp($app['request']->getClientIp())
                    ->setUserAgent($app['request']->headers->get('User-Agent'))
                ;

                $app['orm.em']->persist($participantEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.participants.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.participants.edit',
                        array(
                            'id' => $participantEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/participants/new.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_PARTICIPANTS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $participant = $app['orm.em']->find('Application\Entity\ParticipantEntity', $id);

        if (! $participant) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\ParticipantType(),
            $participant
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $participantEntity = $form->getData();

                $app['orm.em']->persist($participantEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.participants.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.participants.edit',
                        array(
                            'id' => $participantEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['participant'] = $participant;

        return new Response(
            $app['twig']->render(
                'contents/members-area/participants/edit.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_PARTICIPANTS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $participant = $app['orm.em']->find('Application\Entity\ParticipantEntity', $id);

        if (! $participant) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($participant);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.participants.remove.successText'
                    )
                );
            } catch (\Exception $e) {
                $app['flashbag']->add(
                    'danger',
                    $app['translator']->trans(
                        $e->getMessage()
                    )
                );
            }

            return $app->redirect(
                $app['url_generator']->generate(
                    'members-area.participants'
                )
            );
        }

        $data['participant'] = $participant;

        return new Response(
            $app['twig']->render(
                'contents/members-area/participants/remove.html.twig',
                $data
            )
        );
    }
}
