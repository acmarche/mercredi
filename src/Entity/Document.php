<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\FileTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: 'AcMarche\Mercredi\Document\Repository\DocumentRepository')]
class Document implements TimestampableInterface, UuidableInterface
{
    use IdTrait;
    use UuidableTrait;
    use TimestampableTrait;
    use NomTrait;
    use FileTrait;
    /**
     * @Vich\UploadableField(mapping="mercredi_document", fileNameProperty="fileName", mimeType="mimeType", size="fileSize")
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"application/pdf", "application/x-pdf", "image/*"},
     *     mimeTypesMessage="Uniquement des images ou Pdf"
     * )
     */
    private ?File $file = null;

    public function __toString(): string
    {
        return $this->nom;
    }
}
