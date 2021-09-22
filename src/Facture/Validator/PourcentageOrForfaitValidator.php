<?php

namespace AcMarche\Mercredi\Facture\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PourcentageOrForfaitValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \AcMarche\Mercredi\Entity\Facture\FactureReduction */

        if ($value->getPourcentage() && $value->getMontant()) {
            $this->context->buildViolation($constraint->message_only_one)
                ->atPath('facture_reduction[pourcentage]')
                ->addViolation();
        }
    }
}
