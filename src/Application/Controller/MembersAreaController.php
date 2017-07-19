<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MembersAreaController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = [];

        $adminMode = $request->query->get('admin_mode', false);

        if (
            $adminMode &&
            !$app['security.authorization_checker']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $data['adminMode'] = $adminMode;

        return new Response(
            $app['twig']->render('contents/members-area/index.html.twig', $data)
        );
    }

    public function loginAction(Request $request, Application $app)
    {
        $data = [];

        $data['lastUsername'] = $app['session']->get('_security.last_username');
        $data['lastError'] = $app['security.last_error']($request);
        $data['csrfToken'] = $app['csrf.token_manager']->getToken('authenticate');

        return new Response(
            $app['twig']->render(
                'contents/members-area/login.html.twig',
                $data
            )
        );
    }

    public function logoutAction(Request $request, Application $app)
    {
        $data = [];

        return new Response(
            $app['twig']->render(
                'contents/members-area/logout.html.twig',
                $data
            )
        );
    }

    public function registerAction(Request $request, Application $app)
    {
        $data = [];

        if (!$app['settings']['registrationEnabled']) {
            $app->abort(404);
        }

        $code = $request->query->has('code')
            ? $request->query->get('code')
            : false
        ;
        $action = $code
            ? 'confirm'
            : 'register'
        ;

        $form = $app['form.factory']->create(
            \Application\Form\Type\User\RegisterType::class,
            new \Application\Entity\UserEntity()
        );

        if ($action == 'confirm') {
            $userEntity = $app['orm.em']
                ->getRepository('Application\Entity\UserEntity')
                ->findOneByActivationCode($code)
            ;

            if ($userEntity) {
                $userEntity
                    ->setActivationCode(null)
                    ->enable()
                ;

                $app['orm.em']->merge($userEntity);
                $app['orm.em']->flush();

                $app['application.mailer']
                    ->swiftMessageInitializeAndSend([
                        'subject' => $app['name'].' - '.$app['translator']->trans('Welcome'),
                        'to' => [$userEntity->getEmail()],
                        'body' => 'emails/users/register-welcome.html.twig',
                        'type' => 'user.register.welcome',
                        'templateData' => [
                            'user' => $userEntity,
                        ],
                    ])
                ;

                $data['success'] = true;
                $data['successMessage'] = 'members-area.register.confirm.successText';
            } else {
                $data['error'] = true;
                $data['errorMessage'] = 'members-area.register.confirm.activationCodeNotFound';
            }
        } else {
            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $userEntity = $form->getData();

                    $userEntity->setPlainPassword(
                        $userEntity->getPlainPassword(),
                        $app['security.encoder_factory']
                    );

                    $app['application.mailer']
                        ->swiftMessageInitializeAndSend([
                            'subject' => $app['name'].' - '.$app['translator']->trans('Registration'),
                            'to' => [$userEntity->getEmail()],
                            'body' => 'emails/users/register.html.twig',
                            'type' => 'user.register',
                            'templateData' => [
                                'user' => $userEntity,
                            ],
                        ])
                    ;

                    $app['orm.em']->persist($userEntity);
                    $app['orm.em']->flush();

                    $data['success'] = true; // If success, we hide the form
                    $data['successMessage'] = $app['translator']->trans(
                        'members-area.register.successText'
                    );
                }
            }
        }

        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/register.html.twig',
                $data
            )
        );
    }

    public function resetPasswordAction(Request $request, Application $app)
    {
        $data = [];

        $code = $request->query->has('code')
            ? $request->query->get('code')
            : false
        ;
        $action = $code
            ? 'reset'
            : 'request'
        ;

        $form = $app['form.factory']->create(
            new \Application\Form\Type\User\ResetPasswordType($action),
            new \Application\Entity\UserEntity()
        );

        if ($action == 'reset') {
            $userEntity = $app['orm.em']
                ->getRepository('Application\Entity\UserEntity')
                ->findOneByResetPasswordCode($code)
            ;

            if ($userEntity) {
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);

                    if ($form->isValid()) {
                        $temporaryUserEntity = $form->getData();

                        $userEntity
                            ->setResetPasswordCode(null)
                            ->setPlainPassword(
                                $temporaryUserEntity->getPlainPassword(),
                                $app['security.encoder_factory']
                            )
                        ;

                        $app['orm.em']->merge($userEntity);
                        $app['orm.em']->flush();

                        // @to-do: send reset password email success?

                        $data['success'] = true;
                        $data['successMessage'] = $app['translator']->trans(
                            'members-area.request-password.reset.success'
                        );
                    }
                }
            } else {
                $data['error'] = true;
                $data['errorMessage'] = 'members-area.request-password.reset.resetPasswordCodeNotFound';
            }
        } else {
            if ($request->getMethod() == 'POST') {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $temporaryUserEntity = $form->getData();

                    $userEntity = $app['orm.em']
                        ->getRepository('Application\Entity\UserEntity')
                        ->findOneByEmail(
                            $temporaryUserEntity->getEmail()
                        );

                    if ($userEntity) {
                        $userActionEntity = new \Application\Entity\UserActionEntity();
                        $userActionEntity
                          ->setUser($userEntity)
                          ->setKey('user.reset-password.request')
                          ->setType('event')
                          ->setMessage('A new password has been requested!')
                          ->setIp($request->getClientIp())
                          ->setUserAgent($request->headers->get('User-Agent'))
                        ;

                        $userEntity
                            ->setResetPasswordCode(
                                generateRandomString()
                            )
                            ->addUserAction($userActionEntity)
                        ;

                        $app['orm.em']->persist($userEntity);
                        $app['orm.em']->flush();

                        $app['application.mailer']
                            ->swiftMessageInitializeAndSend([
                                'subject' => $app['name'].' - '.$app['translator']->trans('Reset password'),
                                'to' => [$userEntity->getEmail()],
                                'body' => 'emails/users/reset-password.html.twig',
                                'type' => 'user.reset_password',
                                'templateData' => [
                                    'user' => $userEntity,
                                ],
                            ])
                        ;

                        $data['success'] = true; // If success, we hide the form
                        $data['successMessage'] = $app['translator']->trans(
                            'requestPassword.request.success'
                        );
                    } else {
                        $data['error'] = true;
                        $data['errorMessage'] = $app['translator']->trans(
                            'requestPassword.request.emailNotFound'
                        );
                    }
                }
            }
        }

        $data['code'] = $code;
        $data['action'] = $action;
        $data['form'] = $form->createView();

        return new Response(
            $app['twig']->render(
                'contents/members-area/reset-password.html.twig',
                $data
            )
        );
    }
}
