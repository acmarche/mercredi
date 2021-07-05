<?php

namespace AcMarche\Mercredi\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Enfant\Form\EnfantType;
use AcMarche\Mercredi\Enfant\Form\SearchEnfantType;
use AcMarche\Mercredi\Enfant\Handler\EnfantHandler;
use AcMarche\Mercredi\Enfant\Message\EnfantCreated;
use AcMarche\Mercredi\Enfant\Message\EnfantDeleted;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enfant")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class EnfantController extends AbstractController
{
    private const FORM = 'form';
    private const ENFANT = 'enfant';
    private EnfantRepository $enfantRepository;
    private EnfantHandler $enfantHandler;
    private RelationRepository $relationRepository;
    private PresenceRepository $presenceRepository;
    private PresenceUtils $presenceUtils;
    private SearchHelper $searchHelper;

    public function __construct(
        EnfantRepository $enfantRepository,
        EnfantHandler $enfantHandler,
        RelationRepository $relationRepository,
        PresenceRepository $presenceRepository,
        PresenceUtils $presenceUtils,
        SearchHelper $searchHelper
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->enfantHandler = $enfantHandler;
        $this->relationRepository = $relationRepository;
        $this->presenceRepository = $presenceRepository;
        $this->presenceUtils = $presenceUtils;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @Route("/", name="mercredi_admin_enfant_index", methods={"GET","POST"})
     * @Route("/all/{all}", name="mercredi_admin_enfant_all", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchEnfantType::class);
        $form->handleRequest($request);
        $enfants = [];
        $search = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::ENFANT_LIST, $data);
            $search = true;
            $enfants = $this->enfantRepository->search($data['nom'], $data['ecole'], $data['annee_scolaire']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                self::FORM => $form->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/new/{id}", name="mercredi_admin_enfant_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tuteur $tuteur): Response
    {
        $enfant = new Enfant();
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantHandler->newHandle($enfant, $tuteur);
            $this->dispatchMessage(new EnfantCreated($enfant->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/new.html.twig',
            [
                self::ENFANT => $enfant,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_enfant_show", methods={"GET"})
     */
    public function show(Enfant $enfant): Response
    {
        $relations = $this->relationRepository->findByEnfant($enfant);
        $data = $this->presenceRepository->findPresencesByEnfant($enfant);
        $presencesGrouped = $this->presenceUtils->groupByYear($data);
        $frateries = $this->relationRepository->findFrateries($enfant);

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/show.html.twig',
            [
                self::ENFANT => $enfant,
                'frateries' => $frateries,
                'relations' => $relations,
                'prensencesGrouped' => $presencesGrouped,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_enfant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatchMessage(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/edit.html.twig',
            [
                self::ENFANT => $enfant,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_enfant_delete", methods={"POST"})
     */
    public function delete(Request $request, Enfant $enfant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enfant->getId(), $request->request->get('_token'))) {
            $enfantId = $enfant->getId();
            $this->enfantRepository->remove($enfant);
            $this->enfantRepository->flush();
            $this->dispatchMessage(new EnfantDeleted($enfantId));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_index');
    }
}
