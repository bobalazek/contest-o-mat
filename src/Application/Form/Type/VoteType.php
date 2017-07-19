<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('voterUid', TextType::class, [
            'required' => false,
            'label' => 'Voter UID',
        ]);

        $builder->add('entry', EntityType::class, [
            'required' => false,
            'empty_value' => false,
            'class' => 'Application\Entity\EntryEntity',
            'attr' => [
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ],
        ]);

        $builder->add('voteMetas', CollectionType::class, [
            'type' => \Application\Form\Type\VoteMetaType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'prototype' => true,
            'cascade_validation' => true,
            'error_bubbling' => false,
            'by_reference' => false,
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Save',
            'attr' => [
                'class' => 'btn-primary btn-lg btn-block',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\VoteEntity',
            'validation_groups' => ['newAndEdit'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'vote';
    }
}
