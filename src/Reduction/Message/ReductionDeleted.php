<?php

namespace AcMarche\Mercredi\Reduction\Message;

final class ReductionDeleted
{
    public function __construct(
        private int $ecoleId
    ) {
    }

    public function getReductionId(): int
    {
        return $this->ecoleId;
    }
}
