<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/accueil')]
#[IsGranted('ROLE_MERCREDI_PARENT')]
final class AccueilController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private AccueilCalculatorInterface $accueilCalculator,
    ) {}

    #[Route(path: '/{uuid}', name: 'mercredi_parent_accueil_show', methods: ['GET'])]
    #[IsGranted('accueil_show', subject: 'accueil')]
    public function show(Accueil $accueil): Response
    {
        $cout = $this->accueilCalculator->calculate($accueil);

        return $this->render(
            '@AcMarcheMercrediParent/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                'enfant' => $accueil->getEnfant(),
            ],
        );
    }
}
