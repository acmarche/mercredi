<?php

namespace AcMarche\Mercredi\Accueil\Handler;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class AccueilHandler
{
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        AccueilRepository $accueilRepository,
        EnfantRepository $enfantRepository,
        TuteurRepository $tuteurRepository,
        FlashBagInterface $flashBag
    ) {
        $this->accueilRepository = $accueilRepository;
        $this->enfantRepository = $enfantRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->flashBag = $flashBag;
    }

    public function handleNew(Enfant $enfant, Accueil $accueilSubmited)
    {
        if (null !== ($accueil = $this->accueilRepository->isRegistered($accueilSubmited, $enfant))) {
            $this->updateAccueil($accueil, $accueilSubmited);

            return $accueilSubmited;
        }

        if ($accueilSubmited->getDuree() > 0) {
            $this->accueilRepository->persist($accueilSubmited);
            $this->accueilRepository->flush();
        }

        return $accueilSubmited;
    }

    public function handleCollections(array $accueils, array $tuteurs, string $heure)
    {
        foreach ($accueils as $enfantId => $days) {
            foreach ($days as $dateString => $duree) {
                $duree = (int)$duree;

                if (!$enfant = $this->enfantRepository->find((int)$enfantId)) {
                    $this->flashBag->add('danger', 'Référence pour l\enfant '.$enfantId.' non trouvé');

                    return;
                }
                $tuteurId = (int)$tuteurs[$enfantId][0];
                if (!$tuteur = $this->tuteurRepository->find($tuteurId)) {
                    $this->flashBag->add('danger', 'Référence pour le tuteur '.$tuteurId.' non trouvé');

                    return;
                }
                $accueil = new Accueil($tuteur, $enfant);
                $accueil->setDuree($duree);
                $accueil->setHeure($heure);
                try {
                    $date = \DateTime::createFromFormat('Y-m-d', $dateString);
                    $accueil->setDateJour($date);
                    $this->handleNew($enfant, $accueil);
                } catch (\Exception $exception) {
                    $this->flashBag->add(
                        'danger',
                        'Impossible de convertir la date '.$dateString.' pour '.$enfant->getNom(
                        ).' '.$enfant->getPrenom().' '.$exception->getMessage()
                    );

                    return;
                }
            }
        }
    }

    private function updateAccueil(Accueil $accueilExistant, Accueil $accueilSubmited)
    {
        if ($accueilSubmited->getDuree() == 0) {
            $this->accueilRepository->remove($accueilExistant);
        } else {
            $accueilExistant->setHeure($accueilSubmited->getHeure());
            $accueilExistant->setDuree($accueilSubmited->getDuree());
            $accueilExistant->setRemarque($accueilSubmited->getRemarque());
        }
        $this->accueilRepository->flush();
    }
}
