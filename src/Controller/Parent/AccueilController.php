<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/accueil")
 * @IsGranted("ROLE_MERCREDI_PARENT")
 */
final class AccueilController extends AbstractController
{
    use GetTuteurTrait;

    private AccueilCalculatorInterface $accueilCalculator;

    public function __construct(
        AccueilCalculatorInterface $accueilCalculator
    ) {
        $this->accueilCalculator = $accueilCalculator;
    }

    /**
     * @Route("/{uuid}", name="mercredi_parent_accueil_show", methods={"GET"})
     * @IsGranted("accueil_show", subject="accueil")
     */
    public function show(Accueil $accueil): Response
    {
        $cout = $this->accueilCalculator->calculate($accueil);

        return $this->render(
            '@AcMarcheMercrediParent/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                'enfant' => $accueil->getEnfant(),
            ]
        );
    }
}
