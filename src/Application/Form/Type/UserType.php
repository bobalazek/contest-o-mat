<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rolesChoices = $options['app']['userSystemOptions']['roles'];

        $builder->add(
            'profile',
            \Application\Form\Type\ProfileType::class,
            [
                'label' => false,
            ]
        );

        $builder->add('username', TextType::class, [
            'required' => false,
        ]);
        $builder->add('email', EmailType::class);
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_name' => 'password',
            'second_name' => 'repeatPassword',
            'required' => false,
            'invalid_message' => 'errors.user.password.invalidText',
        ]);

        $builder->add('roles', ChoiceType::class, array(
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choices' => $rolesChoices,
        ));

        $builder->add('enabled', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('locked', CheckboxType::class, [
            'required' => false,
        ]);

        $builder->add('Save', SubmitType::class, [
            'attr' => [
                'class' => 'btn-primary btn-lg btn-block',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('app');
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\UserEntity',
            'validation_groups' => function (FormInterface $form) {
                $user = $form->getData();
                $validationGroups = [];

                if ($user->isLocked()) {
                    $validationGroups[] = 'isLocked';
                }

                if ($user->getId()) {
                    $validationGroups[] = 'edit';
                } else {
                    $validationGroups[] = 'new';
                }

                return $validationGroups;
            },
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
