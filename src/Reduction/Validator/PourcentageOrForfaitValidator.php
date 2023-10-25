<?php

namespace AcMarche\Mercredi\Reduction\Validator;

use AcMarche\Mercredi\Entity\Reduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PourcentageOrForfaitValidator extends ConstraintValidator
{
    /**
     * Soi pourcentage soit montant.
     *
     * @param Reduction $reduction
     */
    public function validate($reduction, Constraint $constraint): void
    {
        if ($reduction->pourcentage && $reduction->amount) {
            $this->context->buildViolation($constraint->message_only_one)
                ->atPath('reduction[pourcentage]')
                ->addViolation();
        }

        if ($reduction->is_forfait && $reduction->pourcentage) {
            $this->context->buildViolation($constraint->message_forfait_only_amount)
                ->atPath('reduction[pourcentage]')
                ->addViolation();
        }

        if (!$reduction->pourcentage && !$reduction->amount) {
            $this->context->buildViolation($constraint->message_empty)
                ->atPath('reduction[pourcentage]')
                ->addViolation();
        }
    }
}
