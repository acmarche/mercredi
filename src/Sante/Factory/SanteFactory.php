<?php


namespace AcMarche\Mercredi\Sante\Factory;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Entity\Sante\SanteReponse;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Sante\Repository\SanteReponseRepository;

class SanteFactory
{

    /**
     * @var SanteFicheRepository
     */
    private $santeFicheRepository;
    /**
     * @var SanteReponseRepository
     */
    private $santeReponseRepository;

    public function __construct(
        SanteFicheRepository $santeFicheRepository,
        SanteReponseRepository $santeReponseRepository
    ) {
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeReponseRepository = $santeReponseRepository;
    }

    /**
     * @return SanteFiche
     */
    public function getSanteFicheByEnfant(Enfant $enfant): SanteFiche
    {
        if (!$santeFiche = $this->santeFicheRepository->findOneBy(['enfant' => $enfant])) {
            $santeFiche = new SanteFiche($enfant);
            $this->santeFicheRepository->persist($santeFiche);
        }

        return $santeFiche;
    }

    /**
     * @return SanteReponse
     */
    public function createSanteReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion)
    {
        $santeReponse = new SanteReponse($santeFiche, $santeQuestion);
        $this->santeReponseRepository->persist($santeReponse);

        return $santeReponse;
    }
}
