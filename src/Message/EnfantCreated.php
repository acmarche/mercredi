<?php


namespace AcMarche\Mercredi\Message;


use AcMarche\Mercredi\Entity\Enfant;

class EnfantCreated
{
    /**
     * @var Enfant
     */
    private $enfant;

    public function __construct(Enfant $enfant)
    {
        $this->enfant = $enfant;
    }

    /**
     * @return Enfant
     */
    public function getEnfant(): Enfant
    {
        return $this->enfant;
    }



}
