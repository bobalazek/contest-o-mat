<?php

namespace Application\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordType extends AbstractType
{
    public $action;

    public function __construct($action = '')
    {
        $this->action = $action;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['action'] == 'reset') {
            $builder->add('plainPassword', TextType::class);
        } else {
            $builder->add('email', EmailType::class);
        }

        $builder->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('action');
        $resolver->setDefaults([
            'data_class' => 'Application\Entity\UserEntity',
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'validation_groups' => function (FormInterface $form) use ($resolver) {
                $action = $resolver['action'];
                if ($action == 'reset') {
                    return ['resetPasswordReset'];
                } else {
                    return ['resetPasswordRequest'];
                }
            },
        ]);
    }

    public function getName()
    {
        return ''; // we don't want any input name wrapper
    }
}
