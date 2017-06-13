<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;

use AppBundle\Validator\Constraint\UniqueEmail;
use AppBundle\Validator\Constraint\UniqueUser;

class Registration extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("username", TextType::class, [
            'required' => true,
            'constraints' => [
                new Length([
                    "max" => 100
                ]),
                new UniqueUser()
            ]
        ])
        ->add("email", EmailType::class, [
            'required' => true,
            'constraints' => [
                new Length([
                    "max" => 250
                ]),
                new UniqueEmail()
            ]
        ])
        ->add("password", RepeatedType::class, [
            'type' => PasswordType::class,
            'constraints' => new Length([
                "min" => 6
            ]),
            'invalid_message' => 'The password fields must match.',
            'options' => [
                'attr' => [
                    'class' => 'password'
                ]
            ],
            'first_options'  => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat Password'),
        ])
        ->add("submit", SubmitType::class, [
            'attr' => [
                'class' => 'btn-success',
            ],
            'label' => 'Complete registration'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true
        ));
    }
}
