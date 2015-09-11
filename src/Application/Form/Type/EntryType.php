<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('participant', 'entity', array(
            'required' => false,
            'empty_value' => false,
            'class' => 'Application\Entity\ParticipantEntity',
            'attr' => array(
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ),
        ));

        $builder->add('Save', 'submit', array(
            'attr' => array(
                'class' => 'btn-primary btn-lg btn-block',
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Entity\EntryEntity',
            'validation_groups' => array('newAndEdit'),
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ));
    }

    public function getName()
    {
        return 'entry';
    }
}
