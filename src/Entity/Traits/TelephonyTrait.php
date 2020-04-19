<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait TelephonyTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone_bureau;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $gsm;

}
