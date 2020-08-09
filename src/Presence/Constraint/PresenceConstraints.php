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

    public function addConstraint(PresenceConstraintInterface $constraint) {
        $this->constraints[] = $constraint;
    }

    public function execute($jour): void
    {
        foreach ($this->constraints as $constraint) {
            dump(123);
            $constraint->check($jour);
        }
    }

    public function getConstraints():array
    {
        return $this->constraints;
    }
}
