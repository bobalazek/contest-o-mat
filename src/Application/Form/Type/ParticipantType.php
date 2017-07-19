<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('uid', TextType::class, [
            'required' => false,
        ]);
        $builder->add('name', TextType::class);
        $builder->add('email', EmailType::class);
        $builder->add('via', ChoiceType::class, [
            'choices' => [
                'administration' => 'Administration',
                'application' => 'Application',
            ],
        ]);
        $builder->add('participantMetas', CollectionType::class, [
            'type' => \Application\Form\Type\ParticipantMetaType::class,
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
            'data_class' => 'Application\Entity\ParticipantEntity',
            'validation_groups' => ['newAndEdit'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'participant';
    }
}
