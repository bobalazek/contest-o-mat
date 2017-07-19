<?php

namespace Application\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WinnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('place', NumberType::class);

        $builder->add('prize', TextareaType::class, [
            'required' => false,
        ]);

        $builder->add('participant', EntityType::class, [
            'class' => 'Application\Entity\ParticipantEntity',
            'attr' => [
                'class' => 'select-picker',
                'data-live-search' => 'true',
            ],
        ]);

        $builder->add('entry', EntityType::class, [
            'required' => false,
            'class' => 'Application\Entity\EntryEntity',
            'attr' => [
                'class' => 'select-picker',
                'data-live-search' => 'true',
                'help_text' => 'Optionally, if you want to define more explicit, which entry is "responsible" that the user won.',
            ],
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
            'data_class' => 'Application\Entity\WinnerEntity',
            'validation_groups' => ['newAndEdit'],
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'winner';
    }
}
