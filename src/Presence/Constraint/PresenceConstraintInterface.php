<?php


namespace AcMarche\Mercredi\Presence\Constraint;


use AcMarche\Mercredi\Entity\Jour;

interface PresenceConstraintInterface
{
    public function addFlashError(Jour $jour);

    public function check(Jour $jour): bool;
}
