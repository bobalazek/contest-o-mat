<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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

        $builder->add('registrationEnabled', CheckboxType::class, [
            'label' => 'Registration enabled?',
            'required' => false,
            'data' => $this->app['settings']['registrationEnabled'],
            'attr' => [
                'help_text' => 'In you are for example in the development mode and you want your client to register manually.',
            ],
        ]);

        $builder->add('useSameParticipantDataAfterFirstEntry', CheckboxType::class, [
            'label' => 'Use same participant data after first entry?',
            'required' => false,
            'data' => $this->app['settings']['useSameParticipantDataAfterFirstEntry'],
            'attr' => [
                'help_text' => 'If you want to use the same participant data (that was entered with the first entry) each follow up entry. When un-checked, the cookie will NOT be saved and the participant will be saved over and over again. Exception for this is, if you force the user to use facebook login. Then it will rather look by it\'s Facebook ID (and not the created cookie).',
            ],
        ]);

        $builder->add('useFacebookUserAsParticipantIfPossible', CheckboxType::class, [
            'label' => 'Use facebook user as participant if possible?',
            'required' => false,
            'data' => $this->app['settings']['useFacebookUserAsParticipantIfPossible'],
            'attr' => [
                'help_text' => 'Should we use the facebook SDK?',
            ],
        ]);

        $builder->add('onlyFacebookUsersCanParticipate', CheckboxType::class, [
            'label' => 'Only facebook users can participate?',
            'required' => false,
            'data' => $this->app['settings']['onlyFacebookUsersCanParticipate'],
            'attr' => [
                'help_text' => 'Force users to login via Facebook!',
            ],
        ]);

        $builder->add('entriesArePublic', CheckboxType::class, [
            'label' => 'Entries are public?',
            'required' => false,
            'data' => $this->app['settings']['entriesArePublic'],
            'attr' => [
                'help_text' => 'Should the entries be public? If disabled / un-checked, only admin can view the entries.',
            ],
        ]);

        $builder->add('showWinners', CheckboxType::class, [
            'label' => 'Show winners?',
            'required' => false,
            'data' => $this->app['settings']['showWinners'],
            'attr' => [
                'help_text' => 'Shall the winners (link) be shown? If disabled / un-checked, only admin can view the entries.',
            ],
        ]);

        $builder->add('participateInterval', ChoiceType::class, [
            'label' => 'Participate interval',
            'required' => false,
            'choices' => [
                '' => 'Always (no restrictions)',
                'only_once' => 'Only Once',
                'once_per_day' => 'Once per Day',
            ],
            'data' => $this->app['settings']['participateInterval'],
            'attr' => [
                'help_text' => 'How often can someone participate?',
            ],
        ]);

        $builder->add('voteInterval', ChoiceType::class, [
            'label' => 'Vote interval',
            'required' => false,
            'choices' => [
                '' => 'Always (no restrictions)',
                'only_once' => 'Only Once',
                'once_per_day' => 'Once per Day',
                'only_once_per_entry' => 'Only Once per Entry (allows you still to vote on other entries)',
                'once_per_day_per_entry' => 'Once per Day per Entry (allows you still to vote on other entries)',
            ],
            'data' => $this->app['settings']['voteInterval'],
            'attr' => [
                'help_text' => 'How often can someone vote?',
            ],
        ]);

        $builder->add('startDate', DateTimeType::class, [
            'label' => 'Start date?',
            'required' => false,
            'input' => 'string',
            'data' => $this->app['settings']['startDate'],
            'attr' => [
                'help_text' => 'When should the contest / prizegame start? Leave / set to empty, if there is no start date.',
            ],
        ]);

        $builder->add('endDate', DateTimeType::class, [
            'label' => 'End date?',
            'required' => false,
            'input' => 'string',
            'data' => $this->app['settings']['endDate'],
            'attr' => [
                'help_text' => 'When should the contest / prizegame end? Leave / set to empty, if there is no end date.',
            ],
        ]);

        $builder->add(
            $builder
                ->create('texts', FormType::class, [
                    'label' => false,
                ])
                    ->add('alreadyParticipated', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['alreadyParticipated'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['alreadyParticipated'].'"',
                        ],
                    ])
                    ->add('alreadyParticipatedToday', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['alreadyParticipatedToday'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['alreadyParticipatedToday'].'"',
                        ],
                    ])
                    ->add('thanksForYourParticipation', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['thanksForYourParticipation'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['thanksForYourParticipation'].'"',
                        ],
                    ])
                    ->add('notYetStarted', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['notYetStarted'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['notYetStarted'].'"',
                        ],
                    ])
                    ->add('hasEnded', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['hasEnded'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['hasEnded'].'"',
                        ],
                    ])
                    ->add('onlyFacebookUsersCanParticipate', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['onlyFacebookUsersCanParticipate'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['onlyFacebookUsersCanParticipate'].'"',
                        ],
                    ])
                    ->add('onlyFacebookUsersCanParticipateButton', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['onlyFacebookUsersCanParticipateButton'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['onlyFacebookUsersCanParticipateButton'].'"',
                        ],
                    ])
                    ->add('alreadyVoted', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['alreadyVoted'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVoted'].'"',
                        ],
                    ])
                    ->add('alreadyVotedToday', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['alreadyVotedToday'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVotedToday'].'"',
                        ],
                    ])
                    ->add('alreadyVotedForThisEntry', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['alreadyVotedForThisEntry'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVotedForThisEntry'].'"',
                        ],
                    ])
                    ->add('alreadyVotedForThisEntryToday', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['alreadyVotedForThisEntryToday'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['alreadyVotedForThisEntryToday'].'"',
                        ],
                    ])
                    ->add('terms', TextareaType::class, [
                        'data' => $this->app['settings']['texts']['terms'],
                        'attr' => [
                            'help_text' => 'Original text: "'.$originalTexts['terms'].'". Also accepts HTML.',
                        ],
                    ])
        );

        $builder->add('Save', SubmitType::class, [
            'attr' => [
                'class' => 'btn-primary btn-lg btn-block',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'settings';
    }
}
