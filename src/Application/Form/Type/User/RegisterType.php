<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder
                ->create('profile', FormType::class, [
                        'by_reference' => true,
                        'data_class' => 'Application\Entity\ProfileEntity',
                        'label' => false,
                ])
                    ->add('firstName', TextType::class, [
                        'label' => 'First name',
                    ])
                    ->add('lastName', TextType::class, [
                        'label' => 'Last name',
                        'required' => false,
                    ])
        );

        $builder->add('username', TextType::class, [
            'label' => 'Username',
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'Email',
        ]);
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => 'password',
            'first_name' => 'password',
            'second_name' => 'repeatPassword',
            'required' => false,
            'invalid_message' => 'errors.user.password.invalidText',
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
            'validation_groups' => ['register'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'user';
    }
}
