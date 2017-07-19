<?php

use Symfony\Component\HttpFoundation\Response;

/*========== Index ==========*/
$app->mount(
    '/',
    new Application\ControllerProvider\IndexControllerProvider()
);

/*========== Application ==========*/
$app->mount(
    '/application',
    new Application\ControllerProvider\ApplicationControllerProvider()
);

/*========== API ==========*/
$app->mount(
    '/api',
    new Application\ControllerProvider\ApiControllerProvider()
);

/*========== Members Area ==========*/
$app->mount(
    '/members-area',
    new Application\ControllerProvider\MembersAreaControllerProvider()
);

/******** API ********/
$app->mount(
    '/members-area/api',
    new Application\ControllerProvider\MembersArea\ApiControllerProvider()
);

/******** My ********/
$app->mount(
    '/members-area/my',
    new Application\ControllerProvider\MembersArea\MyControllerProvider()
);

/******** Users ********/
$app->mount(
    '/members-area/users',
    new Application\ControllerProvider\MembersArea\UsersControllerProvider()
);

/******** Participants ********/
$app->mount(
    '/members-area/participants',
    new Application\ControllerProvider\MembersArea\ParticipantsControllerProvider()
);

/******** Entries ********/
$app->mount(
    '/members-area/entries',
    new Application\ControllerProvider\MembersArea\EntriesControllerProvider()
);

/******** Votes ********/
$app->mount(
    '/members-area/votes',
    new Application\ControllerProvider\MembersArea\VotesControllerProvider()
);

/******** Winners ********/
$app->mount(
    '/members-area/winners',
    new Application\ControllerProvider\MembersArea\WinnersControllerProvider()
);

/******** Statistics ********/
$app->mount(
    '/members-area/statistics',
    new Application\ControllerProvider\MembersArea\StatisticsControllerProvider()
);

/******** Settings ********/
$app->mount(
    '/members-area/settings',
    new Application\ControllerProvider\MembersArea\SettingsControllerProvider()
);

/*** Set Locale ***/
$app->match('/set-locale/{locale}', function ($locale) use ($app) {
    $cookie = new \Symfony\Component\HttpFoundation\Cookie(
        'locale',
        $locale,
        new \DateTime('now + 1 year')
    );

    $response = \Symfony\Component\HttpFoundation\Response::create(null, 302, [
        'Location' => isset($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : $app['baseUrl'],
    ]);

    $response->headers->setCookie($cookie);

    return $response;
})
->bind('set-locale')
->assert('locale', implode('|', array_keys($app['locales'])));

/***** Errors *****/
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    if ($code == '403') {
        return $app->redirect(
            $app['url_generator']->generate(
                'index'
            )
        );
    }

    // 404.html, or 40x.html, or 4xx.html, or default.html
    $templates = [
        'contents/errors/'.$code.'.html.twig',
        'contents/errors/'.substr($code, 0, 2).'x.html.twig',
        'contents/errors/'.substr($code, 0, 1).'xx.html.twig',
        'contents/errors/default.html.twig',
    ];

    return new Response(
        $app['twig']->resolveTemplate($templates)->render(
            [
                'code' => $code,
            ]
        ),
        $code
    );
});
