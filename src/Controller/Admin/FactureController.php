<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Form\FactureSendType;
use AcMarche\Mercredi\Facture\Form\FactureType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Message\FactureCreated;
use AcMarche\Mercredi\Facture\Message\FactureDeleted;
use AcMarche\Mercredi\Facture\Message\FactureUpdated;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
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
 * @Route("/facture")
 */
class FactureController extends AbstractController
{
    /**
     * @var FactureRepository
     */
    private $factureRepository;
    /**
     * @var FactureHandler
     */
    private $factureHandler;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var FacturePresenceRepository
     */
    private $facturePresenceRepository;

    public function __construct(
        FactureRepository $factureRepository,
        FactureHandler $factureHandler,
        PresenceRepository $presenceRepository,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureHandler = $factureHandler;
        $this->presenceRepository = $presenceRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
    }

    /**
     * @Route("/{id}/index", name="mercredi_admin_facture_index", methods={"GET","POST"})
     */
    public function index(Tuteur $tuteur): Response
    {
        $factures = $this->factureRepository->findFacturesByTuteur($tuteur);

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $tuteur,
            ]
        );
    }

    /**
     * @Route("/{id}/new", name="mercredi_admin_facture_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tuteur $tuteur): Response
    {
        $facture = $this->factureHandler->newInstance($tuteur);

        $presencesAll = $this->presenceRepository->findPresencesByTuteur($tuteur);
        $presences = [];
        foreach ($presencesAll as $presence) {
            if (!$this->facturePresenceRepository->findByPresence($presence)) {
                $presences[] = $presence;
            }
        }

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureHandler->handleNew($facture, $request->request->get('presences'));

            $this->dispatchMessage(new FactureCreated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/new.html.twig',
            [
                'tuteur' => $tuteur,
                'presences' => $presences,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_admin_facture_show", methods={"GET"})
     */
    public function show(Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/show.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_facture_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureEditType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureRepository->flush();

            $this->dispatchMessage(new FactureUpdated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
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
     * @Route("/{id}/send", name="mercredi_admin_facture_send", methods={"GET","POST"})
     */
    public function send(Request $request, Facture $facture): Response
    {
        $data = [];
        $form = $this->createForm(FactureSendType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureRepository->flush();

            $this->dispatchMessage(new FactureUpdated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/send.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_facture_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Facture $facture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $factureId = $facture->getId();
            $tuteur = $facture->getTuteur();
            $this->factureRepository->remove($facture);
            $this->factureRepository->flush();
            $this->dispatchMessage(new FactureDeleted($factureId));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
    }
}
