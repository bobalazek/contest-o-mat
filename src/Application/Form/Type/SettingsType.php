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

        $builder->add('canParticipateOnlyOnce', 'checkbox', array(
            'label' => 'Can participate only once?',
            'required' => false,
            'data' => $this->app['settings']['canParticipateOnlyOnce'],
            'attr' => array(
                'help_text' => 'If that is the case, we technically do not need entries, rather save al data in the participant meta.',
            ),
        ));

        $builder->add('canParticipateOncePerDay', 'checkbox', array(
            'label' => 'Can participate once per day?',
            'required' => false,
            'data' => $this->app['settings']['canParticipateOncePerDay'],
            'attr' => array(
                'help_text' => 'Shall the user become a "thanks for the participation" mail?',
            ),
        ));

        $builder->add('doIpGeolocation', 'checkbox', array(
            'label' => 'Do IP Geolocation?',
            'required' => false,
            'data' => $this->app['settings']['doIpGeolocation'],
            'attr' => array(
                'help_text' => 'Deprecated; Now we save the IP data directly inside the database, instead of refetching it each time.',
            ),
        ));

        $builder->add('useSameParticipantDataAfterFirstEntry', 'checkbox', array(
            'label' => 'Use same participant data after first entry?',
            'required' => false,
            'data' => $this->app['settings']['useSameParticipantDataAfterFirstEntry'],
            'attr' => array(
                'help_text' => 'If you want to use the same participant data (that you entered with the first entry) each follow up entry.',
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
                'help_text' => 'Force users to use the facebook SDK.',
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
                    'label' => 'Texts',
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
