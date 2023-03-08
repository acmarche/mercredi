<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Utils\StringUtils;
use Doctrine\ORM\Mapping as ORM;

trait RegistreNationalTrait
{
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $registre_national = null;

    public function getRegistreNational(): ?string
    {
        return $this->registre_national;
    }

    public function setRegistreNational(?string $registryNumber): void
    {
        if ($registryNumber) {
            $registryNumber = StringUtils::cleanNationalRegister($registryNumber);
        }
        $this->registre_national = $registryNumber;
    }
}
