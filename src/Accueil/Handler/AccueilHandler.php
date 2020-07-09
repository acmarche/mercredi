<?php


namespace AcMarche\Mercredi\Accueil\Handler;


use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Search\SearchHelper;

class AccueilHandler
{
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;

    public function __construct(
        AccueilRepository $accueilRepository
    ) {
        $this->accueilRepository = $accueilRepository;
    }

    public function handleNew(Enfant $enfant, Accueil $accueilSubmited)
    {
        if ($accueil = $this->accueilRepository->findOneByDateEnfant($accueilSubmited->getDateJour(), $enfant)) {
            $accueil->setMatinApresMidi($accueilSubmited->getMatinApresMidi());
            $accueil->setNbDemiHeure($accueilSubmited->getNbDemiHeure());
            $accueil->setRemarque($accueilSubmited->getRemarque());
            $this->accueilRepository->flush();

            return $accueil;
        }

        $this->accueilRepository->persist($accueilSubmited);

        $this->accueilRepository->flush();

        return $accueilSubmited;
    }
}
