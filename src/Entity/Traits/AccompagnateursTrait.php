<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AccompagnateursTrait
{
    /**
     * @var string[]
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $accompagnateurs = [];

    /**
     * @return string[]
     */
    public function getAccompagnateurs(): array
    {
        return $this->accompagnateurs;
    }

    public function addAccompagnateur(string $accompagnateur)
    {
        $this->accompagnateurs[] = $accompagnateur;

        return $this;
    }

    public function removeAccompagnateur(string $accompagnateur)
    {
        $key = array_search($accompagnateur, $this->accompagnateurs);
        if (isset($this->accompagnateurs[$key])) {
            unset($this->accompagnateurs[$key]);
        }

        return $this;
    }

    public function setAccompagnateurs(?array $accompagnateurs): self
    {
        $this->accompagnateurs = $accompagnateurs;

        return $this;
    }
}
