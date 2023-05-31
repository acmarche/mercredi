<?php

namespace AcMarche\Mercredi\Spam\Handler;

use AcMarche\Mercredi\Entity\Spam;
use AcMarche\Mercredi\Spam\Repository\SpamRepository;

class SpamHandler
{
    public function __construct(private SpamRepository $spamRepository)
    {
    }

    public function isLimit(string $subject): bool
    {
        $spam = $this->instance($subject);

        return $spam->count > 50;
    }

    public function instance(string $subject): Spam
    {
        if (!$spam = $this->spamRepository->findBySubjectAndDate($subject, new \DateTime())) {
            $spam = new Spam($subject);
            $spam->count = 1;
            $spam->created_at = new \DateTime();
            $this->spamRepository->persist($spam);
            $this->spamRepository->flush();
        }

        return $spam;
    }

    public function addCount(string $subject)
    {
        $spam = $this->instance($subject);
        $spam->addCount();
        $this->spamRepository->flush();
    }

}