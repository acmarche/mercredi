<?php

namespace AcMarche\Mercredi\Reduction\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class PourcentageOrForfait extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message_only_one = 'Vous devez appliquer un pourcentage ou un montant fixe';
    public string $message_forfait_only_amount = 'Le forfait n\'est valable que pour un montant fixe';
    public string $message_empty = 'Veuillez encoder un pourcentage ou un montant fixe';

    /**
     * @return array|string
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
