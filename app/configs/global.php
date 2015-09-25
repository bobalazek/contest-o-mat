<?php

return array(
    'environment' => 'development',
    'debug' => true,
    'name' => 'Contest-O-Mat',
    'version' => '0.9.3',
    'author' => 'Borut Balazek',
    // You MUST change the baseUri, in case you'll have multiple instances
    // of this application on the same (sub-)domain.
    'baseUri' => '/',

    // Admin email (& name)
    'email' => 'info@bobalazek.com',
    'emailName' => 'Contest-O-Mat Mailer',

    // Default Locale / Language stuff
    'locale' => 'en_US', // Default locale
    'languageCode' => 'en', // Default language code
    'languageName' => 'English',
    'countryCode' => 'us', // Default country code
    'flagCode' => 'us',
    'dateFormat' => 'd.m.Y',
    'dateTimeFormat' => 'd.m.Y H:i:s',

    'locales' => array( // All available locales
        'en_US' => array(
            'languageCode' => 'en',
            'languageName' => 'English',
            'countryCode' => 'us',
            'flagCode' => 'us',
            'dateFormat' => 'd.m.Y',
            'dateTimeFormat' => 'd.m.Y H:i:s',
        ),
        'de_DE' => array(
            'languageCode' => 'de',
            'languageName' => 'Deutsch',
            'countryCode' => 'de',
            'flagCode' => 'de',
            'dateFormat' => 'd.m.Y',
            'dateTimeFormat' => 'd.m.Y H:i:s',
        ),
    ),

    'environments' => array(
        'development',
        'testing',
        'staging',
        'production',
    ),

    // Time and date
    'currentTime' => date('H:i:s'),
    'currentDate' => date('Y-m-d'),
    'currentDateTime' => date('Y-m-d H:i:s'),

    // Database / Doctrine options
    // http://silex.sensiolabs.org/doc/providers/doctrine.html#parameters
    'databaseOptions' => array(
        'default' => array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'com',
            'user' => 'com',
            'password' => 'com',
            'charset' => 'utf8',
        ),
    ),

    // Swiftmailer options
    // http://silex.sensiolabs.org/doc/providers/swiftmailer.html#parameters
    'swiftmailerOptions' => array(
        'host' => 'corcosoft.com',
        'port' => 465,
        'username' => 'info@corcosoft.com',
        'password' => '',
        'encryption' => 'ssl',
        'auth_mode' => null,
    ),

    // Remmember me Options
    // http://silex.sensiolabs.org/doc/providers/remember_me.html#options
    'rememberMeOptions' => array(
        'key' => 'someRandomKey',
        'name' => 'user',
        'remember_me_parameter' => 'remember_me',
    ),

    // Facebook SDK Options
    // https://developers.facebook.com/docs/php/gettingstarted/5.0.0
    'facebookSdkOptions' => array(
        'id' => '',
        'secret' => '',
        'version' => 'v2.4',
        'permissions' => array(
            'email',
        ),
        // Which fields should be fetched when requesting the facebook graph object (/me)?
        // This fields will also be present when user participates (via facebook).
        // Only if the fields are present.
        'fields' => array(
            'id',
            'name',
            'email',
            'verified',
            'locale',
            'first_name',
            'middle_name',
            'last_name',
            'cover',
        ),
    ),

    // Export Options
    'exportOptions' => array(
        'participants' => array(
            'fields' => array( // The values will be rendered via twig!
                'ID' => '{{ participant.getId() }}',
                'Name' => '{{ participant.getName() }}',
                'Email' => '{{ participant.getEmail() }}',
                'Via' => '{{ participant.getVia() }}',
                'Time' => '{{ participant.getTimeCreated().format("Y-m-d H:i:s") }}',
            ),
        ),
        'entries' => array(
            'fields' => array( // The values will be rendered via twig!
                'ID' => '{{ entry.getId() }}',
                'Participant' => '{{ entry.getParticipant() | raw }}',
                'Answer' => '{{ entry.getMetas("answer") }}', // Example on how to use the metas (works for all - participants, entries and votes. Just call the xxx.getMetas("myAttr") method)
                'Time' => '{{ entry.getTimeCreated().format("Y-m-d H:i:s") }}',
            ),
        ),
        'votes' => array(
            'fields' => array( // The values will be rendered via twig!
                'ID' => '{{ vote.getId() }}',
                'Voter UID' => '{{ vote.getVoterUid() }}',
                'Time' => '{{ vote.getTimeCreated().format("Y-m-d H:i:s") }}',
            ),
        ),
    ),

    // Piwik Options
    'piwikOptions' => array(
        'enabled' => false,
        'url' => 'http://piwik.example.com/', // MUST include the trailing slash!
        'site_id' => 0,
        'token_auth' => '',
    ),

    // Default settings
    'settingsFile' => 'settings.json', // the file inside var/database, which will hold the settings
    'settings' => array(
        'registrationEnabled' => false,
        'participateInterval' => false, // options: false, 'only_once' or 'once_per_day'
        'sendEmailToParticipantOnEntry' => false, // Shall the user become a "thanks for the participation" mail?
        'useSameParticipantDataAfterFirstEntry' => true, // If you want to use the same participant data (that you entered with the first entry) each follow up entry
        'useFacebookUserAsParticipantIfPossible' => true, // Should we use the facebook SDK?
        'onlyFacebookUsersCanParticipate' => false, // If you only want facebook users to participate
        'entriesArePublic' => false, // Shall the entries be public?
        'voteInterval' => false, // options: false, 'only_once' or 'once_per_day'
        'startDate' => false, // or enter a time like '2015-10-01 08:00:00'
        'endDate' => false, // or enter a time like '2015-11-01 00:00:00'
        'texts' => array(
            'alreadyParticipated' => 'You have already participated. Thanks!',
            'alreadyParticipatedToday' => 'You have already participated today. Come back tomorrow. Thanks!',
            'thanksForYourParticipation' => 'You have successfully participated in our prizegame. Thanks!',
            'notYetStarted' => 'The contest has not yet started. Please visit us 1.10.2015 again. Thanks!',
            'hasEnded' => 'The contest has closed. Thanks everybody for participation!',
            'onlyFacebookUsersCanParticipate' => 'This contest only allows facebook users to participate.',
            'onlyFacebookUsersCanParticipateButton' => 'Login into Facebook',
        ),
    ),
);
