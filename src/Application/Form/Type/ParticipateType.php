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
        $builder->add(
			$builder
				->create('participant', 'form', array(
						'by_reference' => true,
						'data_class' => 'Application\Entity\ParticipantEntity',
				))
				    ->add('name', 'text')
                    ->add('email', 'email')
		);

        $builder->add('public', 'checkbox', array(
            'label' => 'You agree with our Terms',
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
