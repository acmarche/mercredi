<?php

namespace AcMarche\Mercredi\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class TemplatedEmailFactory
{
    public static function asPublicEmailJf(): TemplatedEmail
    {
        return new TemplatedEmail();
    }
}
