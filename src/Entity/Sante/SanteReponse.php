<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sante_reponse")
 * @ORM\Entity()
 */
class SanteReponse
{
    use IdTrait;

    /**
     * @var SanteQuestion
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteQuestion" )
     */
    protected $question;

    /**
     * @var SanteFiche
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteFiche", inversedBy="reponses", cascade={"remove"})
     */
    protected $sante_fiche;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $reponse = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $remarque;

    /**
     * @return SanteQuestion
     */
    public function getQuestion(): SanteQuestion
    {
        return $this->question;
    }

    /**
     * @param SanteQuestion $question
     */
    public function setQuestion(SanteQuestion $question): void
    {
        $this->question = $question;
    }

    /**
     * @return SanteFiche
     */
    public function getSanteFiche(): SanteFiche
    {
        return $this->sante_fiche;
    }

    /**
     * @param SanteFiche $sante_fiche
     */
    public function setSanteFiche(SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }

    /**
     * @return bool
     */
    public function isReponse(): bool
    {
        return $this->reponse;
    }

    /**
     * @param bool $reponse
     */
    public function setReponse(bool $reponse): void
    {
        $this->reponse = $reponse;
    }

    /**
     * @return string|null
     */
    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    /**
     * @param string|null $remarque
     */
    public function setRemarque(?string $remarque): void
    {
        $this->remarque = $remarque;
    }

    public function getReponse(): ?bool
    {
        return $this->reponse;
    }

}
