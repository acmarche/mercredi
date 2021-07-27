<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Form\FactureManualType;
use AcMarche\Mercredi\Facture\Form\FacturePayerType;
use AcMarche\Mercredi\Facture\Form\FactureSearchType;
use AcMarche\Mercredi\Facture\Form\FactureSelectMonthType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Message\FactureCreated;
use AcMarche\Mercredi\Facture\Message\FactureDeleted;
use AcMarche\Mercredi\Facture\Message\FacturesCreated;
use AcMarche\Mercredi\Facture\Message\FactureUpdated;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
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
final class FactureController extends AbstractController
{
    private FactureRepository $factureRepository;
    private FactureHandler $factureHandler;
    private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository;
    private FacturePresenceRepository $facturePresenceRepository;

    public function __construct(
        FactureRepository $factureRepository,
        FactureHandler $factureHandler,
        FacturePresenceRepository $facturePresenceRepository,
        FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureHandler = $factureHandler;
        $this->facturePresenceNonPayeRepository = $facturePresenceNonPayeRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
    }

    /**
     * @Route("/{id}/index", name="mercredi_admin_facture_index_by_tuteur", methods={"GET","POST"})
     */
    public function index(Tuteur $tuteur): Response
    {
        $factures = $this->factureRepository->findFacturesByTuteur($tuteur);
        $form = $this->createForm(
            FactureSelectMonthType::class,
            null,
            [
                'action' => $this->generateUrl('mercredi_admin_facture_new_month', ['id' => $tuteur->getId()]),
            ]
        );

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/search", name="mercredi_admin_facture_index", methods={"GET","POST"})
     */
    public function search(Request $request): Response
    {
        $factures = [];
        $form = $this->createForm(FactureSearchType::class);
        $form->handleRequest($request);
        $search = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $search = true;
            $factures = $this->factureRepository->search(
                $dataForm['tuteur'],
                $dataForm['ecole'],
                $dataForm['paye'],
                $dataForm['mois'],
                $dataForm['communication'],
            );
        }

        $formMonth = $this->createForm(
            FactureSelectMonthType::class,
            null,
            [
                'action' => $this->generateUrl('mercredi_admin_facture_new_month_all'),
            ]
        );

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/search.html.twig',
            [
                'factures' => $factures,
                'form' => $form->createView(),
                'formMonth' => $formMonth->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/{id}/manual", name="mercredi_admin_facture_new_manual", methods={"GET","POST"})
     */
    public function newManual(Request $request, Tuteur $tuteur): Response
    {
        $facture = $this->factureHandler->newInstance($tuteur);

        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur);
        $accueils = $this->facturePresenceNonPayeRepository->findAccueilsNonPayes($tuteur);

        $form = $this->createForm(FactureManualType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = $request->request->get('presences', []);
            $accueilsF = $request->request->get('accueils', []);
            $this->factureHandler->handleManually($facture, $presencesF, $accueilsF);

            $this->dispatchMessage(new FactureCreated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/new.html.twig',
            [
                'tuteur' => $tuteur,
                'presences' => $presences,
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/month/", name="mercredi_admin_facture_new_month", methods={"GET","POST"})
     */
    public function newByMonth(Request $request, Tuteur $tuteur): Response
    {
        $form = $this->createForm(FactureSelectMonthType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $month = $form->get('mois')->getData();

            if (!$facture = $this->factureHandler->generateByMonth($tuteur, $month)) {
                $this->addFlash('warning', 'Aucune présences ou accueils non facturés pour ce mois');

                return $this->redirectToRoute('mercredi_admin_facture_index_by_tuteur', ['id' => $tuteur->getId()]);
            }

            $this->dispatchMessage(new FactureCreated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        $this->addFlash('danger', 'Date non valide');

        return $this->redirectToRoute('mercredi_admin_facture_index_by_tuteur', ['id' => $tuteur->getId()]);
    }

    /**
     * @Route("/for/all/", name="mercredi_admin_facture_new_month_all", methods={"GET","POST"})
     */
    public function newByMonthForAll(Request $request): Response
    {
        $form = $this->createForm(FactureSelectMonthType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $month = $form->get('mois')->getData();

            $factures = $this->factureHandler->generateByMonthForAll($month);
            if (count($factures) === 0) {
                $this->addFlash('warning', 'Aucune présences ou accueils non facturés pour ce mois');

                return $this->redirectToRoute('mercredi_admin_facture_new_month_all');
            }

            $this->dispatchMessage(new FacturesCreated($factures));

            return $this->redirectToRoute('mercredi_admin_facture_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/generate.html.twig',
            [
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
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL
        );

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/show.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'facturePresences' => $facturePresences,
                'factureAccueils' => $factureAccueils,
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
     * @Route("/{uuid}/payer", name="mercredi_admin_facture_payer", methods={"GET","POST"})
     */
    public function payer(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FacturePayerType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureRepository->flush();

            $this->addFlash('success', 'Facture payée');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/payer.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_facture_delete", methods={"POST"})
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
