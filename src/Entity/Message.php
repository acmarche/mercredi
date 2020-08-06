<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("message")
 * @ORM\Entity()
 */
class Message implements TimestampableInterface
{
    use IdTrait;
    use TimestampableTrait;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private $from;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $sujet;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $texte;

    /**
     * @var UploadedFile|null
     */
    private $uploadedFile;

    /**
     * @var array|null
     *
     * @ORM\Column(type="array", nullable=false)
     */
    private $destinataires;

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    public function getDestinataires(): ?array
    {
        return $this->destinataires;
    }

    public function setDestinataires(array $destinataires): self
    {
        $this->destinataires = $destinataires;

        return $this;
    }
}
