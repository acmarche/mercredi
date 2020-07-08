<?php

namespace AcMarche\Mercredi\Entity\Security\Traits;

trait IsRoleTrait
{
    public function isParent()
    {
        if (\in_array('ROLE_MERCREDI_PARENT', $this->getRoles())) {
            return true;
        }

        return false;
    }

    public function isAnimateur()
    {
        if (\in_array('ROLE_MERCREDI_ANIMATEUR', $this->getRoles())) {
            return true;
        }

        return false;
    }

    public function isEcole()
    {
        if (\in_array('ROLE_MERCREDI_ECOLE', $this->getRoles())) {
            return true;
        }

        return false;
    }
}
