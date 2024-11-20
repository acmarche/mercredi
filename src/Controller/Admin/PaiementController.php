<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Migration\PaiementRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/paiement')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
class PaiementController extends AbstractController
{
    public function __construct(private PaiementRepository $paiementRepository) {}

    #[Route(path: '/{id}', name: 'mercredi_admin_paiement_index', methods: ['GET'])]
    public function show(Tuteur $tuteur): Response
    {
        $paiements = $this->paiementRepository->findByTuteur($tuteur);

        return $this->render(
            '@AcMarcheMercrediAdmin/paiement/show.html.twig',
            [
                'tuteur' => $tuteur,
                'paiements' => $paiements,
            ],
        );
    }

}