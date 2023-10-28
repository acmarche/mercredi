<?php

namespace AcMarche\Mercredi\Message\Factory;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;

final class MessageFactory
{
    use OrganisationPropertyInitTrait;

    public function createInstance(): Message
    {
        $message = new Message();
        $message->setFrom($this->getEmailSenderAddress());

        return $message;
    }
}
