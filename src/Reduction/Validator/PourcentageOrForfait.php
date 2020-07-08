<?php

namespace AcMarche\Mercredi\Reduction\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PourcentageOrForfait extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message_only_one = 'Vous devez appliquer un pourcentage ou une réduction';

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
