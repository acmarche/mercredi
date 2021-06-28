<?php

namespace AcMarche\Mercredi\Reduction\Message;

final class ReductionCreated
{
    private int $ecoleId;

    public function __construct(int $reductionId)
    {
        $this->ecoleId = $reductionId;
    }

    public function getReductionId(): int
    {
        return $this->ecoleId;
    }
}
