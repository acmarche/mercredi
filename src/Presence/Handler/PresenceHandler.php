<?php

namespace AcMarche\Mercredi\Presence\Handler;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Doctrine\Common\Collections\ArrayCollection;

class PresenceHandler
{
    /**
     * @var ArrayCollection
     */
    private $constraints;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PresenceUtils
     */
    private $presenceUtils;

    public function __construct(PresenceRepository $presenceRepository, PresenceUtils $presenceUtils)
    {
        $this->presenceRepository = $presenceRepository;
        $this->presenceUtils = $presenceUtils;
        $this->constraints = new ArrayCollection();
    }

    public function addConstraint(object $constraint)
    {
        $this->constraints->add($constraint);
    }

    /**
     * @param Jour[] $days
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handleNew(Tuteur $tuteur, Enfant $enfant, iterable $days)
    {
        foreach ($days as $jour) {
            if ($this->presenceRepository->isRegistered($enfant, $jour)) {
                continue;
            }

            if (!$this->checkConstraints($jour)) {
                continue;
            }

            $presence = new Presence($tuteur, $enfant, $jour);
            $this->presenceRepository->persist($presence);
        }
        $this->presenceRepository->flush();
    }

    public function handleForGrouping(Jour $jour, ?Ecole $ecole, bool $displayRemarque): array
    {
        $presences = $this->presenceRepository->findPresencesByJourAndEcole($jour, $ecole);

        $enfants = PresenceUtils::extractEnfants($presences, $displayRemarque);
        $this->presenceUtils->addTelephonesOnEnfants($enfants);
        $data = PresenceUtils::groupByGroupScolaire($enfants);

        return $data;
    }

    public function checkConstraints(Jour $jour): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->check($jour->getDateJour())) {
                $constraint->addFlashError($jour);

                return false;
            }
        }

        return true;
    }
}
