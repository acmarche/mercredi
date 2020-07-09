<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Form\AccueilType;
use AcMarche\Mercredi\Accueil\Form\SearchAccueilType;
use AcMarche\Mercredi\Accueil\Handler\AccueilHandler;
use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Message\AccueilDeleted;
use AcMarche\Mercredi\Accueil\Message\AccueilUpdated;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/accueil")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class AccueilController extends AbstractController
{
    /**
     * @var SearchHelper
     */
    private $searchHelper;
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;
    /**
     * @var AccueilHandler
     */
    private $accueilHandler;

    public function __construct(
        AccueilRepository $accueilRepository,
        AccueilHandler $accueilHandler,
        SearchHelper $searchHelper
    ) {
        $this->searchHelper = $searchHelper;
        $this->accueilRepository = $accueilRepository;
        $this->accueilHandler = $accueilHandler;
    }

    /**
     * @Route("/", name="mercredi_admin_accueil_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchAccueilType::class);
        $form->handleRequest($request);
        $search = false;
        $accueils = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::ACCUEIL_INDEX, $data);
            $search = true;
            $accueils = $this->accueilRepository->search($data['nom']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/index.html.twig',
            [
                'accueils' => $accueils,
                'form' => $form->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/new/{id}", name="mercredi_admin_accueil_new", methods={"GET","POST"})
     */
    public function new(Request $request, Enfant $enfant): Response
    {
        $accueilNew = new Accueil($enfant);
        $form = $this->createForm(AccueilType::class, $accueilNew);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $result =  $this->accueilHandler->handleNew($enfant, $accueilNew);
            $this->dispatchMessage(new AccueilCreated($result->getId()));
                return $this->redirectToRoute('mercredi_admin_accueil_show', ['id' => $accueil->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_accueil_show", methods={"GET"})
     */
    public function show(Accueil $accueil): Response
    {
        $enfant = $accueil->getEnfant();

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'enfant' => $enfant,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_accueil_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Accueil $accueil): Response
    {
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accueilRepository->flush();

            $this->dispatchMessage(new AccueilUpdated($accueil->getId()));

            return $this->redirectToRoute('mercredi_admin_accueil_show', ['id' => $accueil->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/edit.html.twig',
            [
                'accueil' => $accueil,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     *
     * @Route("/{id}", name="mercredi_admin_accueil_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Accueil $accueil): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accueil->getId(), $request->request->get('_token'))) {
            $id = $accueil->getId();
            $enfant = $accueil->getEnfant();
            $this->accueilRepository->remove($accueil);
            $this->accueilRepository->flush();
            $this->dispatchMessage(new AccueilDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
    }
}
