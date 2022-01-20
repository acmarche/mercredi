<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Form\EnfantType;
use AcMarche\Mercredi\Enfant\Form\SearchEnfantType;
use AcMarche\Mercredi\Enfant\Handler\EnfantHandler;
use AcMarche\Mercredi\Enfant\Message\EnfantCreated;
use AcMarche\Mercredi\Enfant\Message\EnfantDeleted;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enfant")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class EnfantController extends AbstractController
{
    public function __construct(
        private EnfantRepository $enfantRepository,
        private EnfantHandler $enfantHandler,
        private RelationRepository $relationRepository,
        private PresenceRepository $presenceRepository,
        private PresenceUtils $presenceUtils,
        private SearchHelper $searchHelper,
        private PlainePresenceRepository $plainePresenceRepository,
        private MessageBusInterface $dispatcher
    ) {

    }

    /**
     * @Route("/", name="mercredi_admin_enfant_index", methods={"GET", "POST"})
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
            $enfants = $this->enfantRepository->search(
                $data['nom'],
                $data['ecole'],
                $data['annee_scolaire'],
                $data['archived']
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/new/{id}", name="mercredi_admin_enfant_new", methods={"GET", "POST"})
     */
    public function new(Request $request, Tuteur $tuteur): Response
    {
        $enfant = new Enfant();
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantHandler->newHandle($enfant, $tuteur);
            $this->dispatcher->dispatch(new EnfantCreated($enfant->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_enfant_show", methods={"GET"})
     */
    public function show(Enfant $enfant): Response
    {
        $relations = $this->relationRepository->findByEnfant($enfant);
        $data = $this->presenceRepository->findByEnfant($enfant);
        $presencesGrouped = $this->presenceUtils->groupByYear($data);
        $fratries = $this->relationRepository->findFrateries($enfant);
        $plaines = $this->plainePresenceRepository->findPlainesByEnfant($enfant);
        $year = date('Y');

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'fratries' => $fratries,
                'relations' => $relations,
                'prensencesGrouped' => $presencesGrouped,
                'plaines' => $plaines,
                'currentYear' => $year,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_enfant_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatcher->dispatch(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
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
            $this->dispatcher->dispatch(new EnfantDeleted($enfantId));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_index');
    }
}
