<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\AccompagnateursTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\PhotoTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use Symfony\Component\Validator\Constraints as Assert;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\BirthdayTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SexeTrait;
use AcMarche\Mercredi\Entity\Traits\UserAddTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Enfant\Repository\EnfantRepository")
 * @Vich\Uploadable
 */
class Enfant implements SluggableInterface, TimestampableInterface
{
    use
        IdTrait,
        NomTrait,
        PrenomTrait,
        BirthdayTrait,
        SexeTrait,
        RemarqueTrait,
        OrdreTrait,
        PhotoTrait,
        AccompagnateursTrait,
        UserAddTrait,
        SluggableTrait,
        ArchiveTrait,
        TimestampableTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $numero_national;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $photo_autorisation = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     */
    private $annee_scolaire;

    /**
     * @var string|null
     *             Forcer le groupe scolaire
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $groupe_scolaire;

    /**
     * @var Ecole|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Ecole")
     */
    private $ecole;
    /**
     * @var Tuteur
     */
    private $parent;

    public function getSluggableFields(): array
    {
        return ['nom', 'prenom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function __toString()
    {
        return mb_strtoupper($this->nom, 'UTF-8').' '.$this->prenom;
    }

    /**
     * @return string|null
     */
    public function getNumeroNational(): ?string
    {
        return $this->numero_national;
    }

    /**
     * @param string|null $numero_national
     */
    public function setNumeroNational(?string $numero_national): void
    {
        $this->numero_national = $numero_national;
    }

    /**
     * @return bool
     */
    public function isPhotoAutorisation(): bool
    {
        return $this->photo_autorisation;
    }

    /**
     * @param bool $photo_autorisation
     */
    public function setPhotoAutorisation(bool $photo_autorisation): void
    {
        $this->photo_autorisation = $photo_autorisation;
    }

    /**
     * @return string|null
     */
    public function getAnneeScolaire(): ?string
    {
        return $this->annee_scolaire;
    }

    /**
     * @param string|null $annee_scolaire
     */
    public function setAnneeScolaire(?string $annee_scolaire): void
    {
        $this->annee_scolaire = $annee_scolaire;
    }

    /**
     * @return string|null
     */
    public function getGroupeScolaire(): ?string
    {
        return $this->groupe_scolaire;
    }

    /**
     * @param string|null $groupe_scolaire
     */
    public function setGroupeScolaire(?string $groupe_scolaire): void
    {
        $this->groupe_scolaire = $groupe_scolaire;
    }

    /**
     * @return Ecole|null
     */
    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    /**
     * @param Ecole|null $ecole
     */
    public function setEcole(?Ecole $ecole): void
    {
        $this->ecole = $ecole;
    }


}
