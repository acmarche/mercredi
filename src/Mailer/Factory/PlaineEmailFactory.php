<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Vich\UploaderBundle\Storage\StorageInterface;

final class PlaineEmailFactory
{
    use OrganisationPropertyInitTrait;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private StorageInterface $storage;

    public function __invoke(): void
    {
        $this->organisation = $this->organisationRepository->getOrganisation();
    }

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

}
