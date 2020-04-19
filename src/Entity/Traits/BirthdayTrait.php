<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait BirthdayTrait
{
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

}
