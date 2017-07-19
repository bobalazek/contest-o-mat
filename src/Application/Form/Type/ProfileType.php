<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'Title',
            'required' => false,
        ]);

        $builder->add('firstName', TextType::class, [
            'label' => 'First name',
        ]);

        $builder->add('middleName', TextType::class, [
            'label' => 'Middle name',
            'required' => false,
        ]);

        $builder->add('lastName', TextType::class, [
            'label' => 'Last name',
            'required' => false,
        ]);

        $builder->add(
            'gender',
            \Application\Form\Type\GenderType::class,
            [
                'label' => 'Gender',
                'required' => false,
            ]
        );

        $builder->add('birthdate', BirthdayType::class, [
            'label' => 'Birthdate',
            'required' => false,
        ]);

        $builder->add('image', FileType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\ProfileEntity',
            'validation_groups' => ['newAndEdit'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'profile';
    }
}
