<?php

namespace AcMarche\Mercredi\Organisation\Traits;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Contracts\Service\Attribute\Required;

trait OrganisationPropertyInitTrait
{
    private OrganisationRepository $organisationRepository;
    private ?Organisation $organisation = null;

    #[Required]
    public function setorganisationRepository(OrganisationRepository $organisationRepository): void
    {
        $this->organisationRepository = $organisationRepository;
        $this->setOrganisation();
    }

    public function setOrganisation(): void
    {
        if (null !== $this->organisationRepository) {
            $this->organisation = $this->organisationRepository->getOrganisation();
        }
    }

    public function getEmailAddressOrganisation(): string
    {
        if (null !== $this->organisation) {
            if (null !== $this->organisation->email_from) {
                return $this->organisation->email_from;
            }

            return $this->organisation->getEmail();
        }

        return 'noemail@domain.be';
    }
}
