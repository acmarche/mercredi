<?php


namespace AcMarche\Mercredi\Animateur\Utils;


use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;

class AnimateurUtils
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(EnfantRepository $enfantRepository, JourRepository $jourRepository)
    {
        $this->enfantRepository = $enfantRepository;
        $this->jourRepository = $jourRepository;
    }

    /**
     * @param Animateur $animateur
     * @return Enfant[]
     */
    public function getAllEnfants(Animateur $animateur): array
    {
        return $this->enfantRepository->findAllForAnimateur($animateur);
    }

    /**
     * @param Animateur $animateur
     * @return Jour[]
     */
    public function getAllJours(Animateur $animateur): array
    {
        return $this->jourRepository->findByAnimateur($animateur);
    }
}
