<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Traits\FileTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity()]
#[ORM\Table(name: 'plaine_groupe')]
#[ORM\UniqueConstraint(columns: ['plaine_id', 'groupe_scolaire_id'])]
class PlaineGroupe implements TimestampableInterface, Stringable
{
    use IdTrait;
    use FileTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Plaine::class, cascade: ['persist'], inversedBy: 'plaine_groupes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plaine $plaine;

    #[ORM\ManyToOne(targetEntity: GroupeScolaire::class)]
    private ?GroupeScolaire $groupe_scolaire;

    #[ORM\Column(type: 'integer')]
    private ?int $inscription_maximum = 0;

    #[Vich\UploadableField(mapping: 'mercredi_groupe', fileNameProperty: 'fileName', size: 'fileSize', mimeType: 'mimeType')]
    #[Assert\File(maxSize: '10M', mimeTypes: [
        'application/pdf',
        'application/x-pdf',
        'image/*',
    ], mimeTypesMessage: 'Uniquement des images ou Pdf')]
    private ?File $file = null;

    /**
     * @var array|Enfant[] $enfants
     */
    public array $enfants=[];

    public function __construct(Plaine $plaine, GroupeScolaire $groupeScolaire)
    {
        $this->plaine = $plaine;
        $this->groupe_scolaire = $groupeScolaire;
    }

    public function __toString(): string
    {
        return $this->getGroupeScolaire()->getNom();
    }

    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    public function setPlaine(?Plaine $plaine): self
    {
        $this->plaine = $plaine;

        return $this;
    }

    public function getInscriptionMaximum(): ?int
    {
        return $this->inscription_maximum;
    }

    public function setInscriptionMaximum(int $inscription_maximum): self
    {
        $this->inscription_maximum = $inscription_maximum;

        return $this;
    }

    public function getGroupeScolaire(): ?GroupeScolaire
    {
        return $this->groupe_scolaire;
    }

    public function setGroupeScolaire(?GroupeScolaire $groupe_scolaire): self
    {
        $this->groupe_scolaire = $groupe_scolaire;

        return $this;
    }
}
