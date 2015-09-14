README
======
**Web Application Starter Kit**

An simple web application boilerplate for min or mid scale applications. Included user system (login, logout, register, forgotten password, profile, edit profile and change password), users, roles, simple statistics and more.

Build status
-------------------
[![Build Status](https://travis-ci.org/bobalazek/web-application-starter-kit.svg?branch=master)](https://travis-ci.org/bobalazek/web-application-starter-kit)

Requirements & Tools & Helpers
-------------------
* PHP > 5.3.9
* [Composer](https://getcomposer.org/) *
* [Bower](http://bower.io/) *
* [PHP Coding Standards Fixer](http://cs.sensiolabs.org/)

Setup
-------------------
* Navigate yor your web directory: `cd /var/www`
* Create a new project: `composer create-project bobalazek/web-application-starter-kit --no-scripts`
* Configure database (and maybe other stuff if you want): [app/configs/global.php](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/configs/global.php#L47) or [app/configs/global-local.php.dist](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/configs/global-local.php.dist) (in case you will deploy it and need a different local configuration. Just rename the global-local.php.dist to global-local.php and set your own configuration)
* Run the following commands:
    * `composer install`
    * `bin/console orm:schema-tool:install --force` (to install the database schema)
    * `bower update` (to install the front-end dependencies - you will need to install [Bower](http://bower.io/) first - if you haven't already)
    * `bin/console application:database:hydrate-data` (to hydrate some data)
* You are done! Start developing!

Development
-------------------
Important files / directory you may want / need to edit when developing your application:

* Config: `app/configs/global.php`
* Templates: `app/templates/contents/application/`
* Participate Form Type: `src/Application/Form/Type/ParticipateType.php`
* Application Controller: `src/Application/Controller/ApplicationController.php`
* Application Controller Provider: `src/Application/ControllerProvider/ApplicationControllerProvider.php`
* Export Functionality:
    * Participants Controller: `src/Application/Controller/MembersArea/ParticipantsController.php` - see the `exportAction`
    * Entries Controller: `src/Application/Controller/MembersArea/EntriesController.php` - see the `exportAction`

Database
-------------------
* We use the Doctrine database
* Navigate to your project directory: `cd /var/www/my-app`
* Check the entities: `bin/console orm:info` (optional)
* Update the schema: `bin/console orm:schema-tool:update --force`
* Database updated!

Application name
-------------------
You should replace the name for your actual application inside the following files:

* `README.md`
* `bower.json`
* `composer.json`
* `phpunit.xml`
* `app/configs/global.php`

Administrator login
-------------------
With the `bin/console application:database:hydrate-data` command, you will, per default hydrate 2 users (which you can change inside the `app/fixtures/users.php` file):

* Admin User (with admin permissions)
    * Username: `admin` or `admin@myapp.com`
    * Password: `test`
* Test User (with the default user permissions)
    * Username: `test` or `test@myapp.com`
    * Password: `test`

Commands
--------------------
* `bin/console application:environment:prepare` - Will create the global-local.php and development-local.php files (if they do not exist)
* `bin/console application:database:hydrate-data [-r|--remove-existing-data]` - Will hydrate the tables with some basic data, like: 2 users and 6 roles (the `--remove-existing-data` flag will truncate all tables before re-hydrating them)
* `bin/console application:storage:prepare` - Will prepare all the storage (var/) folders, like: cache, logs, sessions, etc.
* `bin/console application:translations:prepare` - Prepares all the untranslated string into a separate (app/locales/{locale}_untranslated.yml) file. Accepts an locale argument (defaults to 'en_US' - usage: `bin/console application:translations:prepare --locale de_DE` or `bin/console application:translations:prepare -l de_DE` )

Other commands
----------------------
* `sudo php-cs-fixer fix .` - if you want your code fixed before each commit. You will need to install [PHP Coding Standards Fixer](http://cs.sensiolabs.org/)

License
----------------------
Contest-O-Mat is licensed under the MIT license.
