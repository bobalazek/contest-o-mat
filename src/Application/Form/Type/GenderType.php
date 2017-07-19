<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GenderType extends AbstractType
{
    const MALE = 'male';
    const FEMALE = 'female';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                self::MALE => 'Male',
                self::FEMALE => 'Female',
            ],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getName()
    {
        return 'gender';
    }

    public function getExtendedType()
    {
        return 'gender';
    }
}
