<?php


namespace AcMarche\Mercredi\Sante\Message;

class SanteQuestionUpdated
{
    /**
     * @var int
     */
    private $santeQuestionId;

    public function __construct(int $santeQuestionId)
    {
        $this->santeQuestionId = $santeQuestionId;
    }

    /**
     * @return int
     */
    public function getSanteQuestionId(): int
    {
        return $this->santeQuestionId;
    }
}
