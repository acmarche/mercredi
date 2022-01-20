<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Relation;
use Doctrine\Common\Collections\Collection;

trait RelationsTrait
{
    /**
     * @var Relation[]
     */
    private Collection $relations;

    /**
     * @return Relation[]|Collection
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }

    /**
     * @param Relation[] $relations
     */
    public function setRelations(Collection $relations): void
    {
        $this->relations = $relations;
    }
}
