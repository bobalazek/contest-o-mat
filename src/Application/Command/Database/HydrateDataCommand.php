<?php

namespace Application\Command\Database;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HydrateDataCommand extends ContainerAwareCommand
{
    protected $app;

    public function __construct(
        $name,
        \Silex\Application $app
    ) {
        parent::__construct($name);

        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName(
                'application:database:hydrate-data'
            )
            ->setDescription('Add an Test User to the database')
            ->addOption(
                'remove-existing-data',
                'r',
                InputOption::VALUE_NONE,
                'When the existing data should be removed'
            )
            ->addOption(
                'test-data',
                't',
                InputOption::VALUE_NONE,
                'Enter some test data'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->app;

        $removeExistingData = $input->getOption('remove-existing-data');
        $testData = $input->getOption('test-data');

        if ($removeExistingData) {
            try {
                $app['db']->query('SET foreign_key_checks = 0;');

                $tables = $app['db']->getSchemaManager()->listTables();

                foreach ($tables as $table) {
                    $table = $table->getName();

                    $app['db']->query('TRUNCATE TABLE '.$table.';');
                }

                $app['db']->query('SET foreign_key_checks = 1;');

                $output->writeln('<info>All tables were successfully truncated!</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
        }

        if ($testData) {
            $timeMin = strtotime('-4 weeks');
            $timeMax = strtotime('now');

            for ($i = 0; $i < 1000; ++$i) {
                $participantEntity = new \Application\Entity\ParticipantEntity();
                $entryEntity = new \Application\Entity\EntryEntity();

                $rand = rand(
                    $timeMin,
                    $timeMax
                );

                $randDate = date(
                    'Y-m-d H:i:s',
                    $rand
                );

                $userAgent = \random_uagent();
                $datetime = new \Datetime(
                    $randDate
                );

                $participantEntity
                    ->setVia('administration')
                    ->setName('Participant '.$i)
                    ->setEmail($i.'email@myapp.com')
                    ->setUserAgent($userAgent)
                    ->setIp('127.0.0.1')
                    ->setTimeCreated($datetime)
                    ->setTimeUpdated($datetime)
                ;

                $entryEntity
                    ->setParticipant($participantEntity)
                    ->setUserAgent($userAgent)
                    ->setIp('127.0.0.1')
                    ->setTimeCreated($datetime)
                    ->setTimeUpdated($datetime)
                ;

                $app['orm.em']->persist($participantEntity);
                $app['orm.em']->persist($entryEntity);
            }

            $output->writeln('<info>Participants and Entries were successfully hydrated (with random data)!</info>');
        }

        /***** Users *****/
        $users = include APP_DIR.'/fixtures/users.php';

        foreach ($users as $user) {
            $userEntity = new \Application\Entity\UserEntity();
            $profileEntity = new \Application\Entity\ProfileEntity();

            // Profile
            $profileEntity
                ->setFirstName($user['profile']['firstName'])
                ->setLastName($user['profile']['lastName'])
            ;

            if (isset($user['profile']['gender'])) {
                $profileEntity
                    ->setGender($user['profile']['gender'])
                ;
            }

            if (isset($user['profile']['birthdate'])) {
                $profileEntity
                    ->setBirthdate($user['profile']['birthdate'])
                ;
            }

            // User
            $userEntity
                ->setId($user['id'])
                ->setUsername($user['username'])
                ->setEmail($user['email'])
                ->setPlainPassword(
                    $user['plainPassword'],
                    $app['security.encoder_factory']
                )
                ->setRoles($user['roles'])
                ->setProfile($profileEntity)
                ->enable()
            ;

            $app['orm.em']->persist($userEntity);
        }

        try {
            $app['orm.em']->flush();

            $output->writeln('<info>Data was successfully hydrated!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
    }
}
