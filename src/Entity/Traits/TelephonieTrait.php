<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait TelephonieTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $telephone_bureau;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $gsm;

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     */
    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string|null
     */
    public function getTelephoneBureau(): ?string
    {
        return $this->telephone_bureau;
    }

    /**
     * @param string|null $telephone_bureau
     */
    public function setTelephoneBureau(?string $telephone_bureau): void
    {
        $this->telephone_bureau = $telephone_bureau;
    }

    /**
     * @return string|null
     */
    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    /**
     * @param string|null $gsm
     */
    public function setGsm(?string $gsm): void
    {
        $this->gsm = $gsm;
    }

}
