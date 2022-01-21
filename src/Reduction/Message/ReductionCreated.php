<?php

namespace AcMarche\Mercredi\Reduction\Message;

final class ReductionCreated
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
