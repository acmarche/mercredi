<?php

namespace AcMarche\Mercredi\Security;

final class PasswordGenerator
{
    public static function generatePassword(): string
    {
        $password = '';

        for ($i = 0; $i < 6; ++$i) {
            $password .= rand(1, 9);
        }

        return $password;
    }
}
