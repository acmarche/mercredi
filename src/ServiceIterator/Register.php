<?php

namespace AcMarche\Mercredi\ServiceIterator;

final class Register
{
    public function __construct(
        private iterable $secondaryFlows
    ) {
    }

    public function exe(): void
    {
        foreach ($this->secondaryFlows as $flow) {
            $flow->afterUserRegistrationSuccessful();
        }
    }
}
