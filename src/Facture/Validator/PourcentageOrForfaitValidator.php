<?php

namespace AcMarche\Mercredi\Facture\Validator;

use AcMarche\Mercredi\Entity\Facture\FactureReduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PourcentageOrForfaitValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint FactureReduction */

        if ($value->getPourcentage() && $value->getForfait()) {
            $this->context->buildViolation($constraint->message_only_one)
                ->atPath('facture_reduction[pourcentage]')
                ->addViolation();
        }

        if (!$value->getPourcentage() && !$value->getForfait()) {
            $this->context->buildViolation($constraint->message_only_one)
                ->atPath('facture_reduction[pourcentage]')
                ->addViolation();
        }
    }
}
