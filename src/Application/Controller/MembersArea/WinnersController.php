<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WinnersController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_WINNERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 10);
        $currentPage = $request->query->get('page');

        $winnerResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('w')
            ->from('Application\Entity\WinnerEntity', 'w')
        ;

        $pagination = $app['paginator']->paginate(
            $winnerResults,
            $currentPage,
            $limitPerPage,
            array(
                'route' => 'members-area.winners',
                'defaultSortFieldName' => 'w.place',
                'defaultSortDirection' => 'asc',
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/members-area/winners/index.html.twig',
                $data
            )
        );
    }

    public function exportAction(Request $request, Application $app)
    {
        $exportOptions = $app['exportOptions']['winners'];
        $exportOptionsKeys = array_keys($exportOptions['fields']);
        $exportOptionsValues = array_values($exportOptions['fields']);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $exportOptionsKeys, $exportOptionsValues) {
            $handle = fopen('php://output', 'w+');

            fputcsv(
                $handle,
                $exportOptionsKeys,
                ';'
            );

            $winners = $app['orm.em']
                ->getRepository('Application\Entity\WinnerEntity')
                ->findAll()
            ;

            $twig = clone $app['twig'];
            $twig->setLoader(new \Twig_Loader_String());

            foreach ($winners as $winner) {
                $finalExportOptionsValues = array();

                foreach ($exportOptionsValues as $singleValue) {
                    $finalExportOptionsValues[] = $twig->render(
                        $singleValue,
                        array(
                            'winner' => $winner,
                        )
                    );
                }

                fputcsv(
                    $handle,
                    $finalExportOptionsValues,
                    ';'
                 );
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="winners.csv"');

        return $response;
    }

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_WINNERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\WinnerType(),
            new \Application\Entity\WinnerEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $winnerEntity = $form->getData();

                $app['orm.em']->persist($winnerEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The winner has been saved!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.winners.edit',
                        array(
                            'id' => $winnerEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/winners/new.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_WINNERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $winner = $app['orm.em']->find('Application\Entity\WinnerEntity', $id);

        if (! $winner) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\WinnerType(),
            $winner
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $winnerEntity = $form->getData();

                $app['orm.em']->persist($winnerEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The winner has been edited!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.winners.edit',
                        array(
                            'id' => $winnerEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['winner'] = $winner;

        return new Response(
            $app['twig']->render(
                'contents/members-area/winners/edit.html.twig',
                $data
            )
        );
    }

    public function informAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_WINNERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $winner = $app['orm.em']->find('Application\Entity\WinnerEntity', $id);

        if (! $winner) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $email = $app['application.mailer']
                    ->swiftMessageInitializeAndSend(array(
                        'subject' => $app['name'].' - '.$app['translator']->trans('We have a winner!'),
                        'to' => array(
                            $winner->getParticipant()->getEmail() => $winner->getParticipant()->getName(),
                        ),
                        'body' => 'emails/winners/new.html.twig',
                        'templateData' => array(
                            'winner' => $winner,
                        ),
                    ))
                ;

                $winner
                    ->inform()
                    ->setInformedEmail(
                        $email->getBody()
                    )
                    ->setInformedEmailToken(
                        md5(uniqid(null, true))
                    )
                ;

                $app['orm.em']->persist($winner);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The winner has been informed!'
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
                    'members-area.winners'
                )
            );
        }

        $data['winner'] = $winner;

        return new Response(
            $app['twig']->render(
                'contents/members-area/winners/inform.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_WINNERS_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $winner = $app['orm.em']->find('Application\Entity\WinnerEntity', $id);

        if (! $winner) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($winner);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The winner has been removed!'
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
                    'members-area.winners'
                )
            );
        }

        $data['winner'] = $winner;

        return new Response(
            $app['twig']->render(
                'contents/members-area/winners/remove.html.twig',
                $data
            )
        );
    }
}
