<?php

namespace AppBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Doctrine\ORM\EntityManagerInterface;

class UniqueUserValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $user = $this
            ->em
            ->getRepository("\AppBundle\Entity\User")
            ->findOneBy([
                "name" => $value
            ]);

        if ($user !== null) {
            $this->context->addViolation(
                $constraint->message,
                array('{{ user }}' => $value)
            );
        }
    }
}
