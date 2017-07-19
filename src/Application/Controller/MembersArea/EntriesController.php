<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EntriesController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (!$app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && !$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 10);
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
                'route' => 'members-area.entries',
                'defaultSortFieldName' => 'e.timeCreated',
                'defaultSortDirection' => 'desc',
                'searchFields' => array(
                    'p.name',
                    'p.email',
                    'e.ip',
                    'e.userAgent',
                    'em.value',
                ),
            )
        );

        $data['pagination'] = $pagination;

        return new Response(
            $app['twig']->render(
                'contents/members-area/entries/index.html.twig',
                $data
            )
        );
    }

    public function exportAction(Request $request, Application $app)
    {
        $exportOptions = $app['exportOptions']['entries'];
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

            $entries = $app['orm.em']
                ->getRepository('Application\Entity\EntryEntity')
                ->findAll()
            ;

            $twig = clone $app['twig'];
            $twig->setLoader(new \Twig_Loader_String());

            foreach ($entries as $entry) {
                $finalExportOptionsValues = array();

                foreach ($exportOptionsValues as $singleValue) {
                    $finalExportOptionsValues[] = $twig->render(
                        $singleValue,
                        array(
                            'entry' => $entry,
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
        $response->headers->set('Content-Disposition', 'attachment; filename="entries.csv"');

        return $response;
    }

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (!$app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && !$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\EntryType(),
            new \Application\Entity\EntryEntity()
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entryEntity = $form->getData();
                $entryEntity
                    ->setIp($app['request']->getClientIp())
                    ->setUserAgent($app['request']->headers->get('User-Agent'))
                ;

                $app['orm.em']->persist($entryEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The entry has been added!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.entries.edit',
                        array(
                            'id' => $entryEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/entries/new.html.twig',
                $data
            )
        );
    }

    public function editAction($id, Request $request, Application $app)
    {
        $data = array();

        if (!$app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && !$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $entry = $app['orm.em']->find('Application\Entity\EntryEntity', $id);

        if (!$entry) {
            $app->abort(404);
        }

        $form = $app['form.factory']->create(
            new \Application\Form\Type\EntryType(),
            $entry
        );

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entryEntity = $form->getData();

                $app['orm.em']->persist($entryEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The entry has been edited!'
                    )
                );

                return $app->redirect(
                    $app['url_generator']->generate(
                        'members-area.entries.edit',
                        array(
                            'id' => $entryEntity->getId(),
                        )
                    )
                );
            }
        }

        $data['form'] = $form->createView();
        $data['entry'] = $entry;

        return new Response(
            $app['twig']->render(
                'contents/members-area/entries/edit.html.twig',
                $data
            )
        );
    }

    public function removeAction($id, Request $request, Application $app)
    {
        $data = array();

        if (!$app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && !$app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $entry = $app['orm.em']->find('Application\Entity\EntryEntity', $id);

        if (!$entry) {
            $app->abort(404);
        }

        $confirmAction = $app['request']->query->has('action') &&
            $app['request']->query->get('action') == 'confirm'
        ;

        if ($confirmAction) {
            try {
                $app['orm.em']->remove($entry);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'The entry has been removed!'
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
                    'members-area.entries'
                )
            );
        }

        $data['entry'] = $entry;

        return new Response(
            $app['twig']->render(
                'contents/members-area/entries/remove.html.twig',
                $data
            )
        );
    }
}
