<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints\Length;

class Poll extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("question", TextType::class, [
            'required' => true,
            'constraints' => new Length([
                "max" => 140
            ])
        ])
        ->add("answer1", TextType::class, [
            'required' => true,
            'constraints' => new Length([
                "max" => 32
            ])
        ])
        ->add("answer2", TextType::class, [
            'required' => true,
            'constraints' => new Length([
                "max" => 32
            ])
        ])
        ->add("answer3", TextType::class, [
            'required' => false,
            'constraints' => new Length([
                "max" => 32
            ])
        ])
        ->add("answer4", TextType::class, [
            'required' => false,
            'constraints' => new Length([
                "max" => 32
            ])
        ])
        ->add("answer5", TextType::class, [
            'required' => false,
            'constraints' => new Length([
                "max" => 32
            ])
        ])
        ->add("submit", SubmitType::class, [
            'attr' => [
                'class' => 'btn-success',
            ],
            'label' => 'Create poll'
        ]);
    }
}
