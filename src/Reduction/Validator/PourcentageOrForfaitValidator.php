<?php

namespace AcMarche\Mercredi\Reduction\Validator;

use AcMarche\Mercredi\Entity\Reduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PourcentageOrForfaitValidator extends ConstraintValidator
{
    /**
     * Soi pourcentage soit forfait.
     *
     * @param reduction $reduction
     */
    public function validate($reduction, Constraint $constraint): void
    {
        if ($reduction->getPourcentage() && $reduction->getForfait()) {
            $this->context->buildViolation($constraint->message_only_one)
                ->atPath('reduction[pourcentage]')
                ->addViolation();
        }
    }
}
