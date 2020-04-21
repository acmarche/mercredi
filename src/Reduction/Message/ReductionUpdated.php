<?php


namespace AcMarche\Mercredi\Reduction\Message;

class ReductionUpdated
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
