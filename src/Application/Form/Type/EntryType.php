<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('participant', EntityType::class, [
            'required' => false,
            'empty_value' => false,
            'class' => 'Application\Entity\ParticipantEntity',
            'attr' => [
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ],
        ]);

        $builder->add('entryMetas', CollectionType::class, [
            'type' => new \Application\Form\Type\EntryMetaType(),
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'prototype' => true,
            'cascade_validation' => true,
            'error_bubbling' => false,
            'by_reference' => false,
        ]);

        $builder->add('Save', SubmitType::class, [
            'attr' => [
                'class' => 'btn-primary btn-lg btn-block',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\EntryEntity',
            'validation_groups' => ['newAndEdit'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'entry';
    }
}
