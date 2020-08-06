<?php

namespace AcMarche\Mercredi\Animateur\Message;

final class AnimateurDeleted
{
    /**
     * @var int
     */
    private $animateurId;

    public function __construct(int $animateurId)
    {
        $this->animateurId = $animateurId;
    }

    public function getAnimateurId(): int
    {
        return $this->animateurId;
    }
}
