<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait PrenomTrait
{
      /**
     * @var string|null
     * @ORM\Column(type="string", length=150)
     */
    private $prenom;

    /**
     * @return string|null
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

}
