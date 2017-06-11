<?php

namespace AppBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Doctrine\ORM\EntityManagerInterface;

class UniqueEmailValidator extends ConstraintValidator
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
                "email" => $value
            ]);

        if ($user !== null) {
            $this->context->addViolation(
                $constraint->message,
                array('{{ email }}' => $value)
            );
        }
    }
}
