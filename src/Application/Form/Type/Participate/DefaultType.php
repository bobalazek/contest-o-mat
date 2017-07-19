<?php

namespace Application\Form\Type\Participate;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options['app'];

        // Participant
        /*
         * Show only the first time until the visitor isn't a participant yet.
         * Each time afer that, the participant will automaticall be "attached"
         * to the entry.
         */
        if (!$app['participant']) {
            $nameData = null;
            $emailData = null;

            if ($app['facebookUser']) {
                $nameData = isset($app['facebookUser']->name)
                    ? $app['facebookUser']->name
                    : null
                ;

                $emailData = isset($app['facebookUser']->email)
                    ? $app['facebookUser']->email
                    : null
                ;
            }

            $builder->add(
                $builder
                    ->create('participant', FormType::class, [
                        'label' => false,
                        'by_reference' => true,
                        'data_class' => 'Application\Entity\ParticipantEntity',
                    ])
                        ->add('name', TextType::class, [
                            'data' => $nameData,
                        ])
                        ->add('email', EmailType::class, [
                            'data' => $emailData,
                        ])
                        // Only if you want custom metas for the participant
                        /* ->add(
                            $builder
                                ->create('metas', FormType::class, array(
                                    'label' => false,
                                ))
                                    ->add('age', 'number')
                                    ->add('birthdate', 'date')
                                    ->add('phone_number', 'text')
                        ) */
            );
        }

        // Entry
        $builder->add(
            $builder
                ->create('entry', FormType::class, [
                    'label' => false,
                    'by_reference' => true,
                    'data_class' => 'Application\Entity\EntryEntity',
                ])
                    ->add(
                        $builder
                            ->create('metas', FormType::class, [
                                'label' => false,
                            ])
                                /*
                                 * Since the entries entity depends on metas,
                                 * hydrate the custom metas for each entry here below!
                                 */
                                ->add('answer', TextType::class, [
                                    'label' => 'What is the meaning of life?',
                                ])
                                ->add('me_and_the_hulk_image', FileType::class, [
                                    'label' => 'An image of yourself an the Hulk!',
                                    'constraints' => [
                                        new \Symfony\Component\Validator\Constraints\Image(),
                                    ],
                                ])
                    )
        );

        $termsTemplate = $app['twig']->createTemplate(
            'You agree with our <a href="{{ url(\'application.terms\') }}" target="_blank">Terms</a>'
        );

        $builder->add('terms', CheckboxType::class, [
            'label' => $termsTemplate->render([]),
            'required' => true,
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Submit',
            'attr' => [
                'class' => 'btn-primary btn-lg btn-block',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('app');
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
        ]);
    }

    public function getName()
    {
        return 'participate';
    }
}
