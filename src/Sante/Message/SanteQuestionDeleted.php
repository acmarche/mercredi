<?php

namespace AcMarche\Mercredi\Sante\Message;

final class SanteQuestionDeleted
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
