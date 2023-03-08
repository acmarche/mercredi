<?php

namespace AcMarche\Mercredi\Validator;

use AcMarche\Mercredi\Utils\StringUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegistryNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var RegistryNumber $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if(!StringUtils::cleanNationalRegister($value, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
