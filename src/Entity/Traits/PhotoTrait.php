<?php


namespace AcMarche\Mercredi\Entity\Traits;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait PhotoTrait
{
    /**
     * @Vich\UploadableField(mapping="mercredi_enfant_image", fileNameProperty="photoName")
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\Image(
     *     maxSize="7M"
     * )
     *
     * @var UploadedFile
     */
    protected $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    protected $photoName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $mime;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setPhoto(File $file = null)
    {
        $this->photo = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File|null
     */
    public function getPhoto(): ?File
    {
        return $this->photo;
    }

    /**
     * @return string|null
     */
    public function getPhotoName(): ?string
    {
        return $this->photoName;
    }

    /**
     * @param string|null $photoName
     */
    public function setPhotoName(?string $photoName): void
    {
        $this->photoName = $photoName;
    }

    /**
     * @return string|null
     */
    public function getMime(): ?string
    {
        return $this->mime;
    }

    /**
     * @param string|null $mime
     */
    public function setMime(?string $mime): void
    {
        $this->mime = $mime;
    }

}
