<?php

namespace AcMarche\Mercredi\Security;

class PasswordGenerator
{
    public function generatePassword():string
    {
        $password = '';

        for ($i = 0; $i < 6; ++$i) {
            $password .= rand(1, 9);
        }

        return $password;
    }
}
