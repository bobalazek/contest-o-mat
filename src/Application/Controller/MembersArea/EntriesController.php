<?php

namespace Application\Controller\MembersArea;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EntriesController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $limitPerPage = $request->query->get('limit_per_page', 20);
        $currentPage = $request->query->get('page');

        $entryResults = $app['orm.em']
            ->createQueryBuilder()
            ->select('e')
            ->from('Application\Entity\EntryEntity', 'e')
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

    public function newAction(Request $request, Application $app)
    {
        $data = array();

        if (! $app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
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

                $app['orm.em']->persist($entryEntity);
                $app['orm.em']->flush();

                $app['flashbag']->add(
                    'success',
                    $app['translator']->trans(
                        'members-area.entries.new.successText'
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

        if (! $app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $entry = $app['orm.em']->find('Application\Entity\EntryEntity', $id);

        if (! $entry) {
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
                        'members-area.entries.edit.successText'
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

        if (! $app['security']->isGranted('ROLE_ENTRIES_EDITOR')
            && ! $app['security']->isGranted('ROLE_ADMIN')) {
            $app->abort(403);
        }

        $entry = $app['orm.em']->find('Application\Entity\EntryEntity', $id);

        if (! $entry) {
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
                        'members-area.entries.remove.successText'
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
