<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Facture\Form\FactureAttachType;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
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
    private PresenceRepository $presenceRepository;

    public function __construct(
        FacturePresenceRepository $facturePresenceRepository,
        FactureHandler $factureHandler,
        PresenceRepository $presenceRepository
    ) {
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureHandler = $factureHandler;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * @Route("/{id}/attach", name="mercredi_admin_facture_presence_attach", methods={"GET", "POST"})
     */
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $presences = $this->presenceRepository->findPresencesNonPaysByTuteurAndMonth($tuteur);

        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = $request->request->get('presences', []);
            $this->factureHandler->handleManually($facture, $presencesF, []);

            $this->addFlash('success', 'Les présences ont bien été attachées');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_presence/attach.html.twig',
            [
                'facture' => $facture,
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

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_presence/show.html.twig',
            [
                'facture' => $facture,
                'facturePresence' => $facturePresence,
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
        if ($this->isCsrfTokenValid('delete'.$facturePresence->getId(), $request->request->get('_token'))) {
            $facture = $facturePresence->getFacture();
            $this->facturePresenceRepository->remove($facturePresence);
            $this->facturePresenceRepository->flush();

            $this->addFlash('success', 'La présence a bien été détachée');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
    }
}
