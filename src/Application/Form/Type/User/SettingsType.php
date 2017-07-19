<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'profile',
            \Application\Form\Type\ProfileType::class
        );

        $builder->add('username', TextType::class, [
            'label' => 'Username',
        ]);
        $builder->add('email', EmailType::class, [
            'read_only' => true,
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
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => ['settings'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'cascade_validation' => true,
        ]);
    }

    public function getName()
    {
        return 'user';
    }
}
