<?php

namespace AcMarche\Mercredi\Relation\Message;

class RelationDeleted
{
    /**
     * @var int
     */
    private $relationId;

    public function __construct(int $relationId)
    {
        $this->relationId = $relationId;
    }

    /**
     * @return int
     */
    public function getRelationId(): int
    {
        return $this->relationId;
    }
}
