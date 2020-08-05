<?php

namespace AcMarche\Mercredi\Presence\Constraint;

final class PresenceConstraints
{
    /**
     * @var iterable|PresenceConstraintInterface
     */
    private $constraints;

    public function __construct(iterable $constraints)
    {
        $this->constraints = $constraints;
    }

    public function execute($jour): void
    {
        foreach ($this->constraints as $constraint) {
            dump(123);
            $constraint->check($jour);
        }
    }
}
