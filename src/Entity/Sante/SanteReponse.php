<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("sante_reponse", uniqueConstraints={
 * @ORM\UniqueConstraint(columns={"sante_fiche_id", "question_id"})
 * }) */
final class SanteReponse
{
    use IdTrait;

    /**
     * @var SanteQuestion
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteQuestion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $santeQuestion;

    /**
     * @var SanteFiche
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteFiche", inversedBy="reponses", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $santeFiche;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $reponse = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remarque;

    public function __construct(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        $this->santeFiche = $santeFiche;
        $this->santeQuestion = $santeQuestion;
    }

    public function getQuestion(): SanteQuestion
    {
        return $this->santeQuestion;
    }

    public function setQuestion(SanteQuestion $santeQuestion): void
    {
        $this->santeQuestion = $santeQuestion;
    }

    public function getSanteFiche(): SanteFiche
    {
        return $this->santeFiche;
    }

    public function setSanteFiche(SanteFiche $santeFiche): void
    {
        $this->santeFiche = $santeFiche;
    }

    public function isReponse(): bool
    {
        return $this->reponse;
    }

    public function setReponse(bool $reponse): void
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

    public function getReponse(): ?bool
    {
        return $this->reponse;
    }
}
