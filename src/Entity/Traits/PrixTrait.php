<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait PrixTrait
{
    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    protected $prix1;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    protected $prix2;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    protected $prix3;

}
