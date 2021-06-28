<?php

namespace AcMarche\Mercredi\Organisation\Message;

final class OrganisationUpdated
{
    private int $organisationId;

    public function __construct(int $organisationId)
    {
        $this->organisationId = $organisationId;
    }

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }
}
