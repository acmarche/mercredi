<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PhotoTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SiteWebTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[Vich\Uploadable]
class Organisation implements Stringable
{
    use IdTrait;
    use NomTrait;
    use EmailTrait;
    use AdresseTrait;
    use SiteWebTrait;
    use TelephonieTrait;
    use RemarqueTrait;
    use PhotoTrait;
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $initiale = null;
    /**
     * overload pour nullable false.
     */
    #[ORM\Column(name: 'email', type: 'string', length: 50, nullable: false)]
    #[Assert\Email]
    private ?string $email = null;

    #[Vich\UploadableField(mapping: 'mercredi_organisation_image', fileNameProperty: 'photoName')]
    #[Assert\Image(maxSize: '7M')]
    private ?File $photo = null;

    public function __toString(): string
    {
        return $this->nom;
    }

    public function getInitiale(): ?string
    {
        return $this->initiale;
    }

    public function setInitiale(?string $initiale): self
    {
        $this->initiale = $initiale;

        return $this;
    }
}
