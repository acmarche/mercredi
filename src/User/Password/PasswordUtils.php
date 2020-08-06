<?php

namespace AcMarche\Mercredi\User\Password;

use Symfony\Component\String\ByteString;

final class PasswordUtils
{
    public function generatePassword(): string
    {
        return ByteString::fromRandom(6, '0123456789');
    }
}
