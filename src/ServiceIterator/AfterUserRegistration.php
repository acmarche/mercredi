<?php

namespace AcMarche\Mercredi\ServiceIterator;

interface AfterUserRegistration
{
    public function afterUserRegistrationSuccessful(): void;
}
