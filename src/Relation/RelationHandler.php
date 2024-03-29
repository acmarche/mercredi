<?php

namespace AcMarche\Mercredi\Relation;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Exception;

final class RelationHandler
{
    public function __construct(
        private RelationRepository $relationRepository,
        private EnfantRepository $enfantRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function handleAttachEnfant(Tuteur $tuteur, ?int $enfantId): Relation
    {
        if (! $enfantId) {
            throw new Exception('Enfant non trouvé');
        }

        $enfant = $this->enfantRepository->find($enfantId);
        if (! $enfant instanceof Enfant) {
            throw new Exception('Enfant non trouvé');
        }

        if (null !== $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant)) {
            throw new Exception('L\'enfant est déjà lié à ce parent');
        }

        $relation = new Relation($tuteur, $enfant);
        $this->relationRepository->persist($relation);
        $this->relationRepository->flush();

        return $relation;
    }
}
