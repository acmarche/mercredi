<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("sante_reponse", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"sante_fiche_id", "question_id"})
 * }))
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Sante\Repository\SanteReponseRepository")
 * @UniqueEntity(fields={"sante_fiche", "question"}, message="Une réponse existe déjà")
 */
class SanteReponse
{
    use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteQuestion", inversedBy="reponse")
     * @ORM\JoinColumn(nullable=false)
     */
    private SanteQuestion $question;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteFiche", inversedBy="reponses", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private SanteFiche $sante_fiche;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean")
     */
    private bool $reponse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $remarque = null;

    public function __construct(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        $this->sante_fiche = $santeFiche;
        $this->question = $santeQuestion;
    }

    public function getQuestion(): SanteQuestion
    {
        return $this->question;
    }

    public function setQuestion(SanteQuestion $question): void
    {
        $this->question = $question;
    }

    public function getSanteFiche(): SanteFiche
    {
        return $this->sante_fiche;
    }

    public function setSanteFiche(SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }

    public function isReponse(): bool
    {
        return $this->reponse;
    }

    public function setReponse(?bool $reponse): void
    {
        $this->reponse = $reponse;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): void
    {
        $this->remarque = $remarque;
    }

    public function getReponse(): bool
    {
        return $this->reponse;
    }
}
