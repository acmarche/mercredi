<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait ConjointTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true, options={"comment" = "pere, mere, oncle..."})
     */
    private $relation_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $nom_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $prenom_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone_bureau_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $gsm_conjoint;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email_conjoint;

    public function getRelationConjoint(): ?string
    {
        return $this->relation_conjoint;
    }

    public function setRelationConjoint(?string $relation_conjoint): void
    {
        $this->relation_conjoint = $relation_conjoint;
    }

    public function getNomConjoint(): ?string
    {
        return $this->nom_conjoint;
    }

    public function setNomConjoint(?string $nom_conjoint): void
    {
        $this->nom_conjoint = $nom_conjoint;
    }

    public function getPrenomConjoint(): ?string
    {
        return $this->prenom_conjoint;
    }

    public function setPrenomConjoint(?string $prenom_conjoint): void
    {
        $this->prenom_conjoint = $prenom_conjoint;
    }

    public function getTelephoneConjoint(): ?string
    {
        return $this->telephone_conjoint;
    }

    public function setTelephoneConjoint(?string $telephone_conjoint): void
    {
        $this->telephone_conjoint = $telephone_conjoint;
    }

    public function getTelephoneBureauConjoint(): ?string
    {
        return $this->telephone_bureau_conjoint;
    }

    public function setTelephoneBureauConjoint(?string $telephone_bureau_conjoint): void
    {
        $this->telephone_bureau_conjoint = $telephone_bureau_conjoint;
    }

    public function getGsmConjoint(): ?string
    {
        return $this->gsm_conjoint;
    }

    public function setGsmConjoint(?string $gsm_conjoint): void
    {
        $this->gsm_conjoint = $gsm_conjoint;
    }

    public function getEmailConjoint(): ?string
    {
        return $this->email_conjoint;
    }

    public function setEmailConjoint(?string $email_conjoint): void
    {
        $this->email_conjoint = $email_conjoint;
    }
}
