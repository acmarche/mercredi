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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Organisation\Repository\OrganisationRepository")
 * @Vich\Uploadable
 */
class Organisation
{
    use IdTrait,
        NomTrait,
        EmailTrait,
        AdresseTrait,
        SiteWebTrait,
        TelephonieTrait,
        RemarqueTrait,
        PhotoTrait;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $initiale;

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @Vich\UploadableField(mapping="mercredi_organisation_image", fileNameProperty="photoName")
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\Image(
     *     maxSize="7M"
     * )
     *
     * @var UploadedFile
     */
    private $photo;

    /**
     * @return string|null
     */
    public function getInitiale(): ?string
    {
        return $this->initiale;
    }

    /**
     * @param string|null $initiale
     */
    public function setInitiale(?string $initiale): void
    {
        $this->initiale = $initiale;
    }

}
