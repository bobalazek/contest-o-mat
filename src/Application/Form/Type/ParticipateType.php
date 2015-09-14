<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParticipateType extends AbstractType
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
        if (! $this->app['participant']) {
            $builder->add(
                $builder
                    ->create('participant', 'form', array(
                        'label' => false,
                        'by_reference' => true,
                        'data_class' => 'Application\Entity\ParticipantEntity',
                    ))
                        ->add('name', 'text')
                        ->add('email', 'email')
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
                                ->add('answer', 'text')
                    )
        );

        $builder->add('public', 'checkbox', array(
            'label' => $twig->render(
                'You agree with our <a href="{{ url(\'application.terms\') }}" target="_blank">Terms</a>'
            ),
            'required' => true,
        ));

        $builder->add('Submit', 'submit', array(
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
