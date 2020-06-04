<?php

namespace AcMarche\Mercredi\Relation\Message;

class RelationCreated
{
    /**
     * @var int
     */
    private $relationId;

    public function __construct(int $relationId)
    {
        $this->relationId = $relationId;
    }

    public function getRelationId(): int
    {
        return $this->relationId;
    }
}
