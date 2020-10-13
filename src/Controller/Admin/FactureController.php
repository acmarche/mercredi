<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Form\FactureSearchType;
use AcMarche\Mercredi\Facture\Form\FactureSendType;
use AcMarche\Mercredi\Facture\Form\FactureType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Mailer\FactureMailer;
use AcMarche\Mercredi\Facture\Message\FactureCreated;
use AcMarche\Mercredi\Facture\Message\FactureDeleted;
use AcMarche\Mercredi\Facture\Message\FactureUpdated;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture")
 */
final class FactureController extends AbstractController
{
    /**
     * @var string
     */
    private const TUTEUR = 'tuteur';
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var string
     */
    private const MERCREDI_ADMIN_FACTURE_SHOW = 'mercredi_admin_facture_show';
    /**
     * @var string
     */
    private const ID = 'id';
    /**
     * @var string
     */
    private const FACTURE = 'facture';
    /**
     * @var FactureRepository
     */
    private $factureRepository;
    /**
     * @var FactureHandler
     */
    private $factureHandler;
    /**
     * @var FactureMailer
     */
    private $factureMailer;
    /**
     * @var FactureUtils
     */
    private $factureUtils;

    public function __construct(
        FactureRepository $factureRepository,
        FactureHandler $factureHandler,
        FactureMailer $factureMailer,
        FactureUtils $factureUtils
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureHandler = $factureHandler;
        $this->factureMailer = $factureMailer;
        $this->factureUtils = $factureUtils;
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
                self::TUTEUR => $tuteur,
            ]
        );
    }

    /**
     * @Route("/search", name="mercredi_admin_facture_search", methods={"GET","POST"})
     */
    public function search(Request $request): Response
    {
        $factures = [];
        $form = $this->createForm(FactureSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $factures = $this->factureRepository->search($dataForm[self::TUTEUR], $dataForm['paye']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/search.html.twig',
            [
                'factures' => $factures,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/new", name="mercredi_admin_facture_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tuteur $tuteur): Response
    {
        $facture = $this->factureHandler->newInstance($tuteur);

        $presences = $this->factureUtils->getPresencesNonPayees($tuteur);
        $accueils = $this->factureUtils->getAccueilsNonPayes($tuteur);
        //  $plaines = $this->factureUtils->getPlainesNonPayes($tuteur);

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = $request->request->get('presences', []);
            $accueilsF = $request->request->get('accueils', []);
            $this->factureHandler->handleNew($facture, $presencesF, $accueilsF);

            $this->dispatchMessage(new FactureCreated($facture->getId()));

            return $this->redirectToRoute(self::MERCREDI_ADMIN_FACTURE_SHOW, [self::ID => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/new.html.twig',
            [
                self::TUTEUR => $tuteur,
                'presences' => $presences,
                'accueils' => $accueils,
                //   'plaines'=>$plaines,
                self::FORM => $form->createView(),
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
                self::FACTURE => $facture,
                self::TUTEUR => $tuteur,
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

            return $this->redirectToRoute(self::MERCREDI_ADMIN_FACTURE_SHOW, [self::ID => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/edit.html.twig',
            [
                self::FACTURE => $facture,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/send", name="mercredi_admin_facture_send", methods={"GET","POST"})
     */
    public function sendFacture(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $data = $this->factureMailer->init($facture);
        $form = $this->createForm(FactureSendType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->factureMailer->sendFacture($facture, $data);
                $this->addFlash('success', 'La facture a bien été envoyée');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Une erreur est survenue: '.$e->getMessage());
            }

            return $this->redirectToRoute(self::MERCREDI_ADMIN_FACTURE_SHOW, [self::ID => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/send.html.twig',
            [
                self::FACTURE => $facture,
                self::TUTEUR => $tuteur,
                self::FORM => $form->createView(),
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

        return $this->redirectToRoute('mercredi_admin_tuteur_show', [self::ID => $tuteur->getId()]);
    }
}
