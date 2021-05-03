<?php

namespace AcMarche\Mercredi\Controller\Parent;

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

    /**
     * @var FactureRepository
     */
    private $factureRepository;

    public function __construct(
        FactureRepository $factureRepository
    ) {
        $this->factureRepository = $factureRepository;
    }

    /**
     * @Route("/", name="mercredi_parent_facture_index", methods={"GET","POST"})
     */
    public function index(): Response
    {
        if ($t = $this->hasTuteur()) {
            return $t;
        }
        $factures = $this->factureRepository->findFacturesByTuteur($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $this->tuteur,
            ]
        );
    }
}
