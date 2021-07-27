<?php


namespace AcMarche\Mercredi\Controller\Admin;


use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\FacturePlaine\Handler\FactureHandler;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacturePlaineController extends AbstractController
{
    private FactureHandler $factureHandler;
    private PlaineRepository $plaineRepository;
    private PresenceRepository $presenceRepository;

    public function __construct(
        FactureHandler $factureHandler,
        PlaineRepository $plaineRepository,
        PresenceRepository $presenceRepository
    ) {
        $this->factureHandler = $factureHandler;
        $this->plaineRepository = $plaineRepository;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * @Route("/{id}/manual", name="mercredi_admin_facture_new_plaine", methods={"GET","POST"})
     */
    public function newManual(Request $request, Tuteur $tuteur): Response
    {
        $facture = $this->factureHandler->newInstance($tuteur);
        $plaine = $this->plaineRepository->find(201);
        $presences = $this->presenceRepository->findPresencesByPlaineAndTuteur($plaine, $tuteur);
        $this->factureHandler->handleManually($facture, $plaine);

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/new.html.twig',
            [
                'tuteur' => $tuteur,
                'presences' => $presences,
            ]
        );
    }

}
