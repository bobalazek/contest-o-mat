<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VotesController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_VOTES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 10);
        $currentPage = $request->query->get('page');

        $voteResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('v')
            ->from('Application\Entity\VoteEntity', 'v')
            ->leftJoin('v.voteMetas', 'vm')
        ;

        $pagination = $app['paginator']->paginate(
            $voteResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.votes',
                'defaultSortFieldName' => 'v.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => array(
                    'v.ip',
                    'v.userAgent',
                    'vm.value',
                ),
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/members-area/votes/index.html.twig',
                $data
            )
        );
    }

    public function exportAction(Request $request, Application $app)
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($app) {
            $handle = fopen('php://output', 'w+');

            fputcsv(
                $handle,
                array(
                    'ID',
                    'Entry',
                    'Time',
                ),
                ';'
            );

            $votes = $app['orm.em']
                ->getRepository('Application\Entity\VoteEntity')
                ->findAll()
            ;

            foreach ($votes as $vote) {
                fputcsv(
                    $handle,
                    array(
                        $vote->getId(),
                        $vote->getEntry(),
                        $vote->getTimeCreated()->format(DATE_ATOM),
                    ),
                    ';'
                 );
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="votes.csv"');

        return $response;
    }

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_VOTES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\VoteType(),
            new \Application\Entity\VoteEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $voteEntity = $form->getData();
                $voteEntity
                    ->setIp($app['request']->getClientIp())
                    ->setUserAgent($app['request']->headers->get('User-Agent'))
                ;

                $app['orm.em']->persist($voteEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.votes.new.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.votes.edit',
                        array(
                            'id' => $voteEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/votes/new.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_VOTES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $vote = $app['orm.em']->find('Application\Entity\VoteEntity', $id);

        if (! $vote) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\VoteType(),
            $vote
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $voteEntity = $form->getData();

                $app['orm.em']->persist($voteEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.votes.edit.successText'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.votes.edit',
                        array(
                            'id' => $voteEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['vote'] = $vote;

        return new Response(
            $app['twig']->render(
                'contents/members-area/votes/edit.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_VOTES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $vote = $app['orm.em']->find('Application\Entity\VoteEntity', $id);

        if (! $vote) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($vote);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.votes.remove.successText'
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
                    'members-area.votes'
                )
            );
        }

        $data['vote'] = $vote;

        return new Response(
            $app['twig']->render(
                'contents/members-area/votes/remove.html.twig',
                $data
            )
        );
    }
}
