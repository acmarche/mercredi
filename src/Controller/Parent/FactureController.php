<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Facture\Render\FactureRenderInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_PARENT")
 * @Route("/facture")
 */
final class FactureController extends AbstractController
{
    use GetTuteurTrait;

    private FactureRepository $factureRepository;
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureCalculatorInterface $factureCalculator;
    private factureRenderInterface $factureRender;

    public function __construct(
        FactureRepository $factureRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureCalculatorInterface $factureCalculator,
        factureRenderInterface $factureRender
    ) {
        $this->factureRepository = $factureRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureCalculator = $factureCalculator;
        $this->factureRender = $factureRender;
    }

    /**
     * @Route("/", name="mercredi_parent_facture_index", methods={"GET","POST"})
     */
    public function index(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $factures = $this->factureRepository->findFacturesByTuteurWhoIsSend($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $this->tuteur,
            ]
        );
    }

    /**
     * @Route("/{uuid}/show", name="mercredi_parent_facture_show", methods={"GET"})
     */
    public function show(Facture $facture): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        $html = $this->factureRender->render($facture);


        return $this->render(
            '@AcMarcheMercrediParent/facture/show.html.twig',
            [
                'facture' => $facture,
                'content' => $html,
            ]
        );
    }
}
