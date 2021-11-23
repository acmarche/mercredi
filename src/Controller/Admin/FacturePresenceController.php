<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Form\FactureAttachType;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Calculator\PresenceCalculatorInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_presence")
 */
final class FacturePresenceController extends AbstractController
{
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureHandler $factureHandler;
    private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository;
    private PresenceRepository $presenceRepository;
    private AccueilRepository $accueilRepository;

    public function __construct(
        FacturePresenceRepository $facturePresenceRepository,
        FactureHandler $factureHandler,
        FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository,
        PresenceCalculatorInterface $presenceCalculator,
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository
    ) {
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureHandler = $factureHandler;
        $this->facturePresenceNonPayeRepository = $facturePresenceNonPayeRepository;
        $this->presenceCalculator = $presenceCalculator;
        $this->presenceRepository = $presenceRepository;
        $this->accueilRepository = $accueilRepository;
    }

    /**
     * @Route("/{id}/attach", name="mercredi_admin_facture_presence_attach", methods={"GET", "POST"})
     */
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur);

        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = (array)$request->request->get('presences', []);
            $this->factureHandler->handleManually($facture, $presencesF, []);

            $this->addFlash('success', 'Les présences ont bien été attachées');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
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

    /**
     * @Route("/{id}/show", name="mercredi_admin_facture_presence_show", methods={"GET"})
     */
    public function show(FacturePresence $facturePresence): Response
    {
        $facture = $facturePresence->getFacture();
        $presence = $accueil = null;
        $type = $facturePresence->getObjectType();
        if ($type == FactureInterface::OBJECT_PRESENCE or $type == FactureInterface::OBJECT_PLAINE) {
            $presence = $this->presenceRepository->find($facturePresence->getPresenceId());
        }
        if ($type == FactureInterface::OBJECT_ACCUEIL) {
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

    /**
     * Route("/{id}/edit", name="mercredi_admin_facture_presence_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureEditType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //todo
            echo '';
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/edit.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_facture_presence_delete", methods={"POST"})
     */
    public function delete(Request $request, FacturePresence $facturePresence): Response
    {
        $facture = $facturePresence->getFacture();
        if ($this->isCsrfTokenValid('delete'.$facturePresence->getId(), $request->request->get('_token'))) {

            $this->facturePresenceRepository->remove($facturePresence);
            $this->facturePresenceRepository->flush();

            $this->addFlash('success', 'La présence a bien été détachée');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
    }
}
