<?php

namespace AcMarche\Mercredi\Relation\Message;

final class RelationDeleted
{
    private int $relationId;

    public function __construct(int $relationId)
    {
        $this->relationId = $relationId;
    }

    public function getRelationId(): int
    {
        return $this->relationId;
    }
}
