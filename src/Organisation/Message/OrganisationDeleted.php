<?php

namespace AcMarche\Mercredi\Organisation\Message;

final class OrganisationDeleted
{
    public function __construct(
        private int $organisationId
    ) {
    }

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }
}
