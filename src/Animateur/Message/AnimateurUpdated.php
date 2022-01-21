<?php

namespace AcMarche\Mercredi\Animateur\Message;

final class AnimateurUpdated
{
    public function __construct(
        private int $animateurId
    ) {
    }

    public function getAnimateurId(): int
    {
        return $this->animateurId;
    }
}
