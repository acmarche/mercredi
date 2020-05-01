<?php


namespace AcMarche\Mercredi\Presence\Handler;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

class PresenceHandler
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(PresenceRepository $presenceRepository)
    {
        $this->presenceRepository = $presenceRepository;
    }

    public function handleNew(Tuteur $tuteur, Enfant $enfant, array $days)
    {
        foreach ($days as $jour) {
            if ($this->presenceRepository->exist($enfant, $jour)) {
                continue;
            }
            $presence = new Presence($tuteur, $enfant, $jour);
            $this->presenceRepository->persist($presence);
        }

        $this->presenceRepository->flush();
    }
}
