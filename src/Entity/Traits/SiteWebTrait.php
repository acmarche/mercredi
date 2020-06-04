<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Symfony\Component\Validator\Constraints as Assert;

trait SiteWebTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Url()
     */
    private $site_web;

    public function getSiteWeb(): ?string
    {
        return $this->site_web;
    }

    public function setSiteWeb(?string $site_web): void
    {
        $this->site_web = $site_web;
    }
}
