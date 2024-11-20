<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Form\FactureAttachType;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceNonPayeRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_accueil')]
final class FactureAccueilController extends AbstractController
{
    public function __construct(
        private FactureHandlerInterface $factureHandler,
        private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository,
    ) {}

    #[Route(path: '/{id}/attach', name: 'mercredi_admin_facture_accueil_attach', methods: ['GET', 'POST'])]
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $accueils = $this->facturePresenceNonPayeRepository->findAccueilsNonPayes($tuteur);
        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $accueilsF = (array)$request->request->all('accueils');
            $this->factureHandler->handleManually($facture, [], $accueilsF);

            $this->addFlash('success', 'Les accueils ont bien été attachés');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_accueil/attach.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'accueils' => $accueils,
                'form' => $form->createView(),
            ],
        );
    }
}
