<?php

namespace AcMarche\Mercredi\Presence\Constraint;

final class PresenceConstraints
{
    /**
     * @var iterable|PresenceConstraintInterface
     */
    private $constraints;

    public function __construct()
    {
        $this->constraints = [];
    }

    public function addConstraint(PresenceConstraintInterface $constraint): void
    {
        $this->constraints[] = $constraint;
    }

    public function execute($jour): void
    {
        foreach ($this->constraints as $constraint) {
            $constraint->check($jour);
        }
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }
}
