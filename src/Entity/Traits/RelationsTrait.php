<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Relation;
use Doctrine\Common\Collections\Collection;

trait RelationsTrait
{
    /**
     * @var Relation[]
     */
    private $relations;

    /**
     * @return Relation[]|Collection
     */
    public function getRelations(): iterable
    {
        return $this->relations;
    }

    /**
     * @param Relation[] $relations
     */
    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }
}
