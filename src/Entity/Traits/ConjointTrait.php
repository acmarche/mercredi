<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait ConjointTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true, options={"comment" = "belle-mere, pere, mere"})
     */
    private $conjoint;

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

    /**
     * @return string|null
     */
    public function getConjoint(): ?string
    {
        return $this->conjoint;
    }

    /**
     * @param string|null $conjoint
     */
    public function setConjoint(?string $conjoint): void
    {
        $this->conjoint = $conjoint;
    }

    /**
     * @return string|null
     */
    public function getNomConjoint(): ?string
    {
        return $this->nom_conjoint;
    }

    /**
     * @param string|null $nom_conjoint
     */
    public function setNomConjoint(?string $nom_conjoint): void
    {
        $this->nom_conjoint = $nom_conjoint;
    }

    /**
     * @return string|null
     */
    public function getPrenomConjoint(): ?string
    {
        return $this->prenom_conjoint;
    }

    /**
     * @param string|null $prenom_conjoint
     */
    public function setPrenomConjoint(?string $prenom_conjoint): void
    {
        $this->prenom_conjoint = $prenom_conjoint;
    }

    /**
     * @return string|null
     */
    public function getTelephoneConjoint(): ?string
    {
        return $this->telephone_conjoint;
    }

    /**
     * @param string|null $telephone_conjoint
     */
    public function setTelephoneConjoint(?string $telephone_conjoint): void
    {
        $this->telephone_conjoint = $telephone_conjoint;
    }

    /**
     * @return string|null
     */
    public function getTelephoneBureauConjoint(): ?string
    {
        return $this->telephone_bureau_conjoint;
    }

    /**
     * @param string|null $telephone_bureau_conjoint
     */
    public function setTelephoneBureauConjoint(?string $telephone_bureau_conjoint): void
    {
        $this->telephone_bureau_conjoint = $telephone_bureau_conjoint;
    }

    /**
     * @return string|null
     */
    public function getGsmConjoint(): ?string
    {
        return $this->gsm_conjoint;
    }

    /**
     * @param string|null $gsm_conjoint
     */
    public function setGsmConjoint(?string $gsm_conjoint): void
    {
        $this->gsm_conjoint = $gsm_conjoint;
    }

    /**
     * @return string|null
     */
    public function getEmailConjoint(): ?string
    {
        return $this->email_conjoint;
    }

    /**
     * @param string|null $email_conjoint
     */
    public function setEmailConjoint(?string $email_conjoint): void
    {
        $this->email_conjoint = $email_conjoint;
    }

}
