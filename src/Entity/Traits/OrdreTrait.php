<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait OrdreTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=2, nullable=true, options={"comment" = "1,2, suviant", "default" = "0"})
     * @Assert\NotBlank()
     */
    private $ordre = 0;

    /**
     * @return int
     */
    public function getOrdre(): int
    {
        return $this->ordre;
    }

    /**
     * @param int $ordre
     */
    public function setOrdre(int $ordre): void
    {
        $this->ordre = $ordre;
    }

}
