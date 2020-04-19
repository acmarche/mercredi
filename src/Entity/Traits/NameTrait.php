<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait NameTrait
{

    /**
     * @var string
     * @ORM\Column(type="string", length=150)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(type="string", length=150)
     */
    private $prenom;

}
