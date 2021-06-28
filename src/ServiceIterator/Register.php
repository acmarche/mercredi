<?php

namespace AcMarche\Mercredi\ServiceIterator;


final class Register
{
    private iterable $secondaryFlows;

    public function __construct(iterable $secondaryFlows)
    {
        $this->secondaryFlows = $secondaryFlows;
    }

    public function exe(): void
    {
        foreach ($this->secondaryFlows as $flow) {
            $flow->afterUserRegistrationSuccessful();
        }
    }
}
