<?php

namespace AcMarche\Mercredi\Presence\Repository;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Utils\SortUtils;

final class PresenceDaysProvider implements PresenceDaysProviderInterface
{
    private JourRepository $jourRepository;
    private PresenceUtils $presenceUtils;

    public function __construct(JourRepository $jourRepository, PresenceUtils $presenceUtils)
    {
        $this->jourRepository = $jourRepository;
        $this->presenceUtils = $presenceUtils;
    }

    /**
     * @return Jour[]
     */
    public function getAllDaysToSubscribe(Enfant $enfant): array
    {
        $deadLineDatePresence = $this->presenceUtils->getDeadLineDatePresence();
        $jours = $this->jourRepository->findJourByDateGreatherOrEqual($deadLineDatePresence, $enfant);

        $deadLineDatePedagogique = $this->presenceUtils->getDeadLineDatePedagogique();
        $pedagogiques = $this->jourRepository->findPedagogiqueByDateGreatherOrEqual($deadLineDatePedagogique, $enfant);

        $all = array_merge($jours, $pedagogiques);

        return SortUtils::sortJoursByDateTime($all);
    }
}
