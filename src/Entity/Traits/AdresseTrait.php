<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait AdresseTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $rue;

    /**
     * @var string|null
     *
     * @ORM\Column(type="integer", length=6, nullable=true)
     */
    private $code_postal;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $localite;

}
