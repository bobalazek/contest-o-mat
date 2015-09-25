<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingsType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configFileContents = require APP_DIR.'/configs/global.php';
        $originalTexts = $configFileContents['settings']['texts'];

        $builder->add('registrationEnabled', 'checkbox', array(
            'label' => 'Registration enabled?',
            'required' => false,
            'data' => $this->app['settings']['registrationEnabled'],
            'attr' => array(
                'help_text' => 'In you are for example in the development mode and you want your client to register manually.',
            ),
        ));

        $builder->add('useSameParticipantDataAfterFirstEntry', 'checkbox', array(
            'label' => 'Use same participant data after first entry?',
            'required' => false,
            'data' => $this->app['settings']['useSameParticipantDataAfterFirstEntry'],
            'attr' => array(
                'help_text' => 'If you want to use the same participant data (that was entered with the first entry) each follow up entry. When un-checked, the cookie will NOT be saved and the participant will be saved over and over again. Exception for this is, if you force the user to use facebook login. Then it will rather look by it\'s Facebook ID (and not the created cookie).',
            ),
        ));

        $builder->add('useFacebookUserAsParticipantIfPossible', 'checkbox', array(
            'label' => 'Use facebook user as participant if possible?',
            'required' => false,
            'data' => $this->app['settings']['useFacebookUserAsParticipantIfPossible'],
            'attr' => array(
                'help_text' => 'Should we use the facebook SDK?',
            ),
        ));

        $builder->add('onlyFacebookUsersCanParticipate', 'checkbox', array(
            'label' => 'Only facebook users can participate?',
            'required' => false,
            'data' => $this->app['settings']['onlyFacebookUsersCanParticipate'],
            'attr' => array(
                'help_text' => 'Force users to login via Facebook!',
            ),
        ));

        $builder->add('entriesArePublic', 'checkbox', array(
            'label' => 'Entries are public?',
            'required' => false,
            'data' => $this->app['settings']['entriesArePublic'],
            'attr' => array(
                'help_text' => 'Should the entries be public?',
            ),
        ));

        $builder->add('participateInterval', 'choice', array(
            'label' => 'Participate interval',
            'required' => false,
            'choices' => array(
                '' => 'Always (no restrictions)',
                'only_once' => 'Only Once',
                'once_per_day' => 'Once per Day',
            ),
            'data' => $this->app['settings']['participateInterval'],
            'attr' => array(
                'help_text' => 'How often can someone participate?',
            ),
        ));

        $builder->add('voteInterval', 'choice', array(
            'label' => 'Vote interval',
            'required' => false,
            'choices' => array(
                '' => 'Always (no restrictions)',
                'only_once' => 'Only Once',
                'once_per_day' => 'Once per Day',
                'only_once_per_entry' => 'Only Once per Entry (allows you still to vote on other entries)',
                'once_per_day_per_entry' => 'Once per Day per Entry (allows you still to vote on other entries)',
            ),
            'data' => $this->app['settings']['voteInterval'],
            'attr' => array(
                'help_text' => 'How often can someone vote?',
            ),
        ));

        $builder->add('startDate', 'datetime', array(
            'label' => 'Start date?',
            'required' => false,
            'input' => 'string',
            'data' => $this->app['settings']['startDate'],
            'attr' => array(
                'help_text' => 'When should the contest / prizegame start? Leave / set to empty, if there is no start date.',
            ),
        ));

        $builder->add('endDate', 'datetime', array(
            'label' => 'End date?',
            'required' => false,
            'input' => 'string',
            'data' => $this->app['settings']['endDate'],
            'attr' => array(
                'help_text' => 'When should the contest / prizegame end? Leave / set to empty, if there is no end date.',
            ),
        ));

        $builder->add(
            $builder
                ->create('texts', 'form', array(
                    'label' => false,
                ))
                    ->add('alreadyParticipated', 'textarea', array(
                        'data' => $this->app['settings']['texts']['alreadyParticipated'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['alreadyParticipated'].'"',
                        ),
                    ))
                    ->add('alreadyParticipatedToday', 'textarea', array(
                        'data' => $this->app['settings']['texts']['alreadyParticipatedToday'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['alreadyParticipatedToday'].'"',
                        ),
                    ))
                    ->add('thanksForYourParticipation', 'textarea', array(
                        'data' => $this->app['settings']['texts']['thanksForYourParticipation'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['thanksForYourParticipation'].'"',
                        ),
                    ))
                    ->add('notYetStarted', 'textarea', array(
                        'data' => $this->app['settings']['texts']['notYetStarted'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['notYetStarted'].'"',
                        ),
                    ))
                    ->add('hasEnded', 'textarea', array(
                        'data' => $this->app['settings']['texts']['hasEnded'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['hasEnded'].'"',
                        ),
                    ))
                    ->add('onlyFacebookUsersCanParticipate', 'textarea', array(
                        'data' => $this->app['settings']['texts']['onlyFacebookUsersCanParticipate'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['onlyFacebookUsersCanParticipate'].'"',
                        ),
                    ))
                    ->add('onlyFacebookUsersCanParticipateButton', 'textarea', array(
                        'data' => $this->app['settings']['texts']['onlyFacebookUsersCanParticipateButton'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['onlyFacebookUsersCanParticipateButton'].'"',
                        ),
                    ))
                    ->add('alreadyVoted', 'textarea', array(
                        'data' => $this->app['settings']['texts']['alreadyVoted'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVoted'].'"',
                        ),
                    ))
                    ->add('alreadyVotedToday', 'textarea', array(
                        'data' => $this->app['settings']['texts']['alreadyVotedToday'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVotedToday'].'"',
                        ),
                    ))
                    ->add('alreadyVotedForThisEntry', 'textarea', array(
                        'data' => $this->app['settings']['texts']['alreadyVotedForThisEntry'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVotedForThisEntry'].'"',
                        ),
                    ))
                    ->add('alreadyVotedForThisEntryToday', 'textarea', array(
                        'data' => $this->app['settings']['texts']['alreadyVotedForThisEntryToday'],
                        'attr' => array(
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVotedForThisEntryToday'].'"',
                        ),
                    ))
        );

        $builder->add('Save', 'submit', array(
            'attr' => array(
                'class' => 'btn-primary btn-lg btn-block',
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ));
    }

    public function getName()
    {
        return 'settings';
    }
}
