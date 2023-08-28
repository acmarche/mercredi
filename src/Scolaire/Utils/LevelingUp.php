<?php

namespace AcMarche\Mercredi\Scolaire\Utils;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository;

class LevelingUp
{
    public function __construct(
        private EnfantRepository $enfantRepository,
        private AnneeScolaireRepository $anneeScolaireRepository
    ) {
    }

    /**
     * @return Enfant[]
     */
    public function prepare(): array
    {
        $enfants = $this->enfantRepository->findAllActif();

        foreach ($enfants as $enfant) {
            $enfant->nextYear = $this->anneeScolaireRepository->findNext($enfant->getAnneeScolaire());
        }

        return $enfants;
    }

    public function up(): void
    {
        $enfants = $this->enfantRepository->findAllActif();
        foreach ($enfants as $enfant) {
            if (!$enfant->nextYear = $this->anneeScolaireRepository->findNext($enfant->getAnneeScolaire())) {
                $enfant->setArchived(true);
            }
            $enfant->setAnneeScolaire($enfant->nextYear);
        }
        $this->enfantRepository->flush();
    }


}