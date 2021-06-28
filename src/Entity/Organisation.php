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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Organisation\Repository\OrganisationRepository")
 * @Vich\Uploadable
 */
class Organisation
{
    use IdTrait;
    use NomTrait;
    use EmailTrait;
    use AdresseTrait;
    use SiteWebTrait;
    use TelephonieTrait;
    use RemarqueTrait;
    use PhotoTrait;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $initiale = null;

    /**
     * overload pour nullable false.
     *
     * @Assert\Email()
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private ?string $email = null;

    /**
     * @Vich\UploadableField(mapping="mercredi_organisation_image", fileNameProperty="photoName")
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\Image(
     *     maxSize="7M"
     * )
     */
    private ?File $photo = null;

    public function __toString()
    {
        return $this->nom;
    }

    public function getInitiale(): ?string
    {
        return $this->initiale;
    }

    public function setInitiale(?string $initiale): void
    {
        $this->initiale = $initiale;
    }
}
