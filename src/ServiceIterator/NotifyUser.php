<?php

namespace AcMarche\Mercredi\ServiceIterator;

class NotifyUser implements AfterUserRegistration
{
    public function afterUserRegistrationSuccessful(): void
    {
        dump(46);
    }
}
