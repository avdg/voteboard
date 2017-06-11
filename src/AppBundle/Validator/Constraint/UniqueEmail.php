<?php

namespace AppBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class UniqueEmail extends Constraint
{
    public $message = 'Email "{{ email }}" has already been used';

    public function validatedBy()
    {
        return get_class($this) . "Validator";
    }
}
