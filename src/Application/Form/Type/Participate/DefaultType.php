<?php

namespace Application\Form\Type\Participate;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DefaultType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Fix, so the url gets parsed thought twig
        $twig = clone $this->app['twig'];
        $twig->setLoader(new \Twig_Loader_String());

        // Participant
        /*
         * Show only the first time until the visitor isn't a participant yet.
         * Each time afer that, the participant will automaticall be "attached"
         * to the entry.
         */
        if (! $this->app['participant']) {
            $nameData = null;
            $emailData = null;

            if ($this->app['facebookUser']) {
                $nameData = isset($this->app['facebookUser']->name)
                    ? $this->app['facebookUser']->name
                    : null
                ;

                $emailData = isset($this->app['facebookUser']->email)
                    ? $this->app['facebookUser']->email
                    : null
                ;
            }

            $builder->add(
                $builder
                    ->create('participant', 'form', array(
                        'label' => false,
                        'by_reference' => true,
                        'data_class' => 'Application\Entity\ParticipantEntity',
                    ))
                        ->add('name', 'text', array(
                            'data' => $nameData,
                        ))
                        ->add('email', 'email', array(
                            'data' => $emailData,
                        ))
                        // Only if you want custom metas for the participant
                        /* ->add(
                            $builder
                                ->create('metas', 'form', array(
                                    'label' => false,
                                ))
                                    ->add('age', 'number')
                                    ->add('birthdate', 'date')
                                    ->add('phone_number', 'text')
                        ) */
            );
        }

        // Entry
        $builder->add(
            $builder
                ->create('entry', 'form', array(
                    'label' => false,
                    'by_reference' => true,
                    'data_class' => 'Application\Entity\EntryEntity',
                ))
                    ->add(
                        $builder
                            ->create('metas', 'form', array(
                                'label' => false,
                            ))
                                /*
                                 * Since the entries entity depends on metas,
                                 * hydrate the custom metas for each entry here below!
                                 */
                                ->add('answer', 'text', array(
                                    'label' => 'What is the meaning of life?',
                                ))
                    )
        );

        $builder->add('terms', 'checkbox', array(
            'label' => $twig->render(
                'You agree with our <a href="{{ url(\'application.terms\') }}" target="_blank">Terms</a>'
            ),
            'required' => true,
        ));

        $builder->add('submit', 'submit', array(
            'label' => 'Submit',
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
        return 'participate';
    }
}
