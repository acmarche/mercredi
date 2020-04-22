<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait PrixTrait
{
    /**
     * @var float
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    private $prix1;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    private $prix2;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    private $prix3;

    /**
     * @return float
     */
    public function getPrix1(): float
    {
        return $this->prix1;
    }

    /**
     * @param float $prix1
     */
    public function setPrix1(float $prix1): void
    {
        $this->prix1 = $prix1;
    }

    /**
     * @return float
     */
    public function getPrix2(): float
    {
        return $this->prix2;
    }

    /**
     * @param float $prix2
     */
    public function setPrix2(float $prix2): void
    {
        $this->prix2 = $prix2;
    }

    /**
     * @return float
     */
    public function getPrix3(): float
    {
        return $this->prix3;
    }

    /**
     * @param float $prix3
     */
    public function setPrix3(float $prix3): void
    {
        $this->prix3 = $prix3;
    }

}
