<?php

namespace AppBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class UniqueUser extends Constraint
{
    public $message = 'User "{{ user }}" already exists';

    public function validatedBy()
    {
        return get_class($this) . "Validator";
    }
}
