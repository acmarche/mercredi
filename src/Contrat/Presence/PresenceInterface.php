<?php

namespace AcMarche\Mercredi\Contrat\Presence;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Reduction;
use AcMarche\Mercredi\Entity\Tuteur;
use DateTimeInterface;

interface PresenceInterface
{
    public function __toString(): string;

    public function getAbsent(): int;

    public function setAbsent(int $absent): void;

    public function getEnfant(): ?Enfant;

    public function setEnfant(Enfant $enfant): void;

    public function getId(): ?int;

    public function getJour(): Jour;

    public function setJour(Jour $jour): void;

    public function getOrdre(): int;

    public function setOrdre(int $ordre): void;

    public function getReduction(): ?Reduction;

    public function setReduction(?Reduction $reduction): void;

    public function isHalf(): bool;

    public function setHalf(bool $half): void;

    public function getRemarque(): ?string;

    public function setRemarque(?string $remarque): void;

    public function getCreatedAt(): DateTimeInterface;

    public function getUpdatedAt(): DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): void;

    public function setUpdatedAt(DateTimeInterface $updatedAt): void;

    public function updateTimestamps(): void;

    public function getTuteur(): ?Tuteur;

    public function setTuteur(Tuteur $tuteur): void;

    public function getUserAdd(): ?string;

    public function setUserAdd(?string $userAdd): void;
}
