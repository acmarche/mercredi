<?php


namespace AcMarche\Mercredi\Entity\Traits;


use AcMarche\Mercredi\Entity\Relation;
use Doctrine\ORM\Mapping as ORM;

trait RelationsTrait
{
    /**
     * @var Relation[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Relation", mappedBy="tuteur", cascade={"remove"})
     */
    private $relations;
}
