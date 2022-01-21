<?php

namespace AcMarche\Mercredi\Presence\Repository;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Utils\SortUtils;

final class PresenceDaysProvider implements PresenceDaysProviderInterface
{
    public function __construct(
        private JourRepository $jourRepository,
        private PresenceUtils $presenceUtils
    ) {
    }

    /**
     * @return Jour[]
     */
    public function getAllDaysToSubscribe(Enfant $enfant): array
    {
        $deadLineDatePresence = $this->presenceUtils->getDeadLineDatePresence();
        $jours = $this->jourRepository->findJourNotPedagogiqueByDateGreatherOrEqualAndNotRegister($deadLineDatePresence, $enfant);

        $deadLineDatePedagogique = $this->presenceUtils->getDeadLineDatePedagogique();
        $pedagogiques = $this->jourRepository->findPedagogiqueByDateGreatherOrEqualAndNotRegister($deadLineDatePedagogique, $enfant);

        $all = array_merge($jours, $pedagogiques);

        return SortUtils::sortJoursByDateTime($all);
    }
}
