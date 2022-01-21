<?php

namespace AcMarche\Mercredi\Sante\Factory;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Entity\Sante\SanteReponse;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

final class SanteFactory
{
    public function __construct(
        private SanteFicheRepository $santeFicheRepository,
        private SanteReponseRepository $santeReponseRepository
    ) {
    }

    public function getSanteFicheByEnfant(Enfant $enfant): SanteFiche
    {
        if (null === ($santeFiche = $this->santeFicheRepository->findOneBy([
            'enfant' => $enfant,
        ]))) {
            $santeFiche = new SanteFiche($enfant);
            $this->santeFicheRepository->persist($santeFiche);
        }

        return $santeFiche;
    }

    public function createSanteReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion): SanteReponse
    {
        $santeReponse = new SanteReponse($santeFiche, $santeQuestion);
        $this->santeReponseRepository->persist($santeReponse);

        return $santeReponse;
    }
}
