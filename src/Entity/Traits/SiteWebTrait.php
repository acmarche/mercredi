<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SiteWebTrait
{
    /**
     * @var string|null
     * @Assert\Url()
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $site_web = null;

    public function getSiteWeb(): ?string
    {
        return $this->site_web;
    }

    public function setSiteWeb(?string $site_web): void
    {
        $this->site_web = $site_web;
    }
}
