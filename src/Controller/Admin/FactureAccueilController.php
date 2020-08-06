<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;
use AcMarche\Mercredi\Facture\Form\FactureAttachType;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_accueil")
 */
final class FactureAccueilController extends AbstractController
{
    /**
     * @var FactureAccueilRepository
     */
    private $factureAccueilRepository;
    /**
     * @var FactureHandler
     */
    private $factureHandler;
    /**
     * @var FactureUtils
     */
    private $factureUtils;
    /**
     * @var string
     */
    private const FACTURE = 'facture';

    public function __construct(
        FactureAccueilRepository $factureAccueilRepository,
        FactureHandler $factureHandler,
        FactureUtils $factureUtils
    ) {
        $this->factureAccueilRepository = $factureAccueilRepository;
        $this->factureHandler = $factureHandler;
        $this->factureUtils = $factureUtils;
    }

    /**
     * @Route("/{id}/attach", name="mercredi_admin_facture_accueil_attach", methods={"GET", "POST"})
     */
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $accueils = $this->factureUtils->getAccueilsNonPayes($tuteur);

        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accueilsF = $request->request->get('accueils', []);
            $this->factureHandler->handleNew($facture, [], $accueilsF);

            $this->addFlash('success', 'Les accueils ont bien été attachés');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_accueil/attach.html.twig',
            [
                self::FACTURE => $facture,
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_admin_facture_accueil_show", methods={"GET"})
     */
    public function show(FactureAccueil $factureAccueil): Response
    {
        $facture = $factureAccueil->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_accueil/show.html.twig',
            [
                self::FACTURE => $facture,
                'factureAccueil' => $factureAccueil,
            ]
        );
    }

    /**
     * Route("/{id}/edit", name="mercredi_admin_facture_accueil_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureEditType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //todo ?
            echo '';
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/edit.html.twig',
            [
                self::FACTURE => $facture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_facture_accueil_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FactureAccueil $factureAccueil): Response
    {
        if ($this->isCsrfTokenValid('delete'.$factureAccueil->getId(), $request->request->get('_token'))) {
            $facture = $factureAccueil->getFacture();
            $this->factureAccueilRepository->remove($factureAccueil);
            $this->factureAccueilRepository->flush();

            $this->addFlash('success', 'L\'accueil a bien été détaché');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
    }
}
