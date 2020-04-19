<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait NameTrait
{
    /**
     * @var string
     * @ORM\Column(type="string", length=150)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(type="string", length=150)
     */
    private $prenom;

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPrenom(): string
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
