<?php

namespace AcMarche\Mercredi\Spam\Handler;

use AcMarche\Mercredi\Entity\History;
use AcMarche\Mercredi\Spam\Repository\HistoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SpamHandler
{
    public function __construct(
        private RateLimiterFactory $anonymousApiLimiter,
        private HistoryRepository $spamRepository
    ) {
    }

    public function isAccepted(Request $request): bool
    {
        $limiter = $this->anonymousApiLimiter->create($request->getClientIp());

        return $limiter->consume()->isAccepted();
    }

    public function instance(string $subject): History
    {
        if (!$spam = $this->spamRepository->findBySubjectAndDate($subject, new \DateTime())) {
            $spam = new History($subject);
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