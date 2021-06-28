<?php

namespace AcMarche\Mercredi\Enfant\Handler;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

final class EnfantHandler
{
    private EnfantRepository $enfantRepository;
    private RelationRepository $relationRepository;

    public function __construct(EnfantRepository $enfantRepository, RelationRepository $relationRepository)
    {
        $this->enfantRepository = $enfantRepository;
        $this->relationRepository = $relationRepository;
    }

    public function newHandle(Enfant $enfant, Tuteur $tuteur): void
    {
        $this->enfantRepository->persist($enfant);
        $relation = new Relation($tuteur, $enfant);
        $this->relationRepository->persist($relation);
        $this->enfantRepository->flush();
        $this->relationRepository->flush();
    }
}
