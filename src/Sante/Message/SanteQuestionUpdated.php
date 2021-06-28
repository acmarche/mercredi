<?php

namespace AcMarche\Mercredi\Sante\Message;

final class SanteQuestionUpdated
{
    private int $santeQuestionId;

    public function __construct(int $santeQuestionId)
    {
        $this->santeQuestionId = $santeQuestionId;
    }

    public function getSanteQuestionId(): int
    {
        return $this->santeQuestionId;
    }
}
