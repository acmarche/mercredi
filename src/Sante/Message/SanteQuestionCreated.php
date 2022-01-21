<?php

namespace AcMarche\Mercredi\Sante\Message;

final class SanteQuestionCreated
{
    public function __construct(
        private int $santeQuestionId
    ) {
    }

    public function getSanteQuestionId(): int
    {
        return $this->santeQuestionId;
    }
}
