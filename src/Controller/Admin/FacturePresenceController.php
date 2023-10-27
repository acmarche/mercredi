<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Form\FactureAttachType;
use AcMarche\Mercredi\Facture\Form\PresenceFactureAttachType;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_presence')]
final class FacturePresenceController extends AbstractController
{
    public function __construct(
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureRepository $factureRepository,
        private FactureHandlerInterface $factureHandler,
        private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository,
        public PresenceCalculatorInterface $presenceCalculator,
        private PresenceRepository $presenceRepository,
        private AccueilRepository $accueilRepository
    ) {
    }

    #[Route(path: '/{id}/attach', name: 'mercredi_admin_facture_presence_attach', methods: ['GET', 'POST'])]
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur);
        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = $request->request->all('presences');
            $this->factureHandler->handleManually($facture, $presencesF, []);

            $this->addFlash('success', 'Les présences ont bien été attachées');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_presence/attach.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'presences' => $presences,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/attach/from-presence', name: 'mercredi_admin_facture_presence_attach_from_presence', methods: [
        'GET',
        'POST',
    ])]
    public function attachFromPresence(Request $request, Presence $presence): Response
    {
        $tuteur = $presence->getTuteur();
        $factures = $this->factureRepository->findByTuteurNotPaid($tuteur);

        $form = $this->createForm(PresenceFactureAttachType::class, null, ['factures' => $factures]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $facture = $form->get('facture')->getData();

            if ($facture instanceof Facture) {
                $this->factureHandler->handleManually($facture, [$presence->getId()], []);

                $this->addFlash('success', 'La présence ont bien été attachée');

                return $this->redirectToRoute('mercredi_admin_facture_show', [
                    'id' => $facture->getId(),
                ]);
            }
            $this->addFlash('danger', 'Aucune facture sélectionnée');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_presence/attach_from_presence.html.twig',
            [
                'tuteur' => $tuteur,
                'presence' => $presence,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'mercredi_admin_facture_presence_show', methods: ['GET'])]
    public function show(FacturePresence $facturePresence): Response
    {
        $facture = $facturePresence->getFacture();
        $presence = $accueil = null;
        $type = $facturePresence->getObjectType();
        if (FactureInterface::OBJECT_PRESENCE === $type || FactureInterface::OBJECT_PLAINE === $type) {
            $presence = $this->presenceRepository->find($facturePresence->getPresenceId());
        }
        if (FactureInterface::OBJECT_ACCUEIL === $type) {
            $accueil = $this->accueilRepository->find($facturePresence->getPresenceId());
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_presence/show.html.twig',
            [
                'facture' => $facture,
                'facturePresence' => $facturePresence,
                'presence' => $presence,
                'accueil' => $accueil,
            ]
        );
    }

    #[Route(path: '/{id}/detach', name: 'mercredi_admin_facture_presence_detach', methods: ['POST'])]
    public function detach(Request $request, FacturePresence $facturePresence): RedirectResponse
    {
        $facture = $facturePresence->getFacture();
        if ($this->isCsrfTokenValid('delete'.$facturePresence->getId(), $request->request->get('_token'))) {
            $this->facturePresenceRepository->remove($facturePresence);
            $this->facturePresenceRepository->flush();
            $presenceId = $facturePresence->getPresenceId();

            $this->addFlash('success', 'La présence a bien été détachée');

            if ($presence = $this->presenceRepository->find($presenceId)) {
                return $this->redirectToRoute('mercredi_admin_presence_show', [
                    'id' => $presence->getId(),
                ]);
            }
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }
}
