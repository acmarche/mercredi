<?php


namespace AcMarche\Mercredi\Presence\Repository;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;

interface PresenceDaysProviderInterface
{
    /**
     * @return Jour[]
     */
    public function getAllDaysToSubscribe(Enfant $enfant): array
   ;
}
