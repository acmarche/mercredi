<?php


namespace AcMarche\Mercredi\Presence\Utils;


use AcMarche\Mercredi\Entity\Presence;

class PresenceUtils
{
    /**
     * @param Presence[] $presences
     * @return array
     */
    public function groupByYear(array $presences): array
    {
        $all = [];
        foreach ($presences as $presence) {
            $jour = $presence->getJour()->getDateJour();
            $all[$jour->format('Y')][$jour->format('m')][] = $presence;
        }

        return $all;
    }

}
