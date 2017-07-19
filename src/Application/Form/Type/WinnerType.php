<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WinnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('place', 'number');

        $builder->add('prize', 'textarea', array(
            'required' => false,
        ));

        $builder->add('participant', 'entity', array(
            'class' => 'Application\Entity\ParticipantEntity',
            'attr' => array(
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ),
        ));

        $builder->add('entry', 'entity', array(
            'required' => false,
            'class' => 'Application\Entity\EntryEntity',
            'attr' => array(
                'class' => 'select-picker',
                'data-live-search' => 'true',
                'help_text' => 'Optionally, if you want to define more explicit, which entry is "responsible" that the user won.',
            ),
        ));

        $builder->add('submit', 'submit', array(
            'label' => 'Save',
            'attr' => array(
                'class' => 'btn-primary btn-lg btn-block',
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\WinnerEntity',
            'validation_groups' => array('newAndEdit'),
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ));
    }

    public function getName()
    {
        return 'winner';
    }
}
