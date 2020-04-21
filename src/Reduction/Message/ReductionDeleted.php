<?php

namespace AcMarche\Mercredi\Reduction\Message;

class ReductionDeleted
{
    /**
     * @var int
     */
    private $ecoleId;

    public function __construct(int $reductionId)
    {
        $this->ecoleId = $reductionId;
    }

    /**
     * @return int
     */
    public function getReductionId(): int
    {
        return $this->ecoleId;
    }
}
