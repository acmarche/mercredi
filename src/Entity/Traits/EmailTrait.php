<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait EmailTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

}
