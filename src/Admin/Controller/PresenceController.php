<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Dto\JourListing;
use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Presence\Form\PresenceNewType;
use AcMarche\Mercredi\Presence\Form\PresenceType;
use AcMarche\Mercredi\Presence\Form\SearchPresenceByMonthType;
use AcMarche\Mercredi\Presence\Form\SearchPresenceType;
use AcMarche\Mercredi\Presence\Handler\PresenceHandler;
use AcMarche\Mercredi\Presence\Message\PresenceCreated;
use AcMarche\Mercredi\Presence\Message\PresenceDeleted;
use AcMarche\Mercredi\Presence\Message\PresenceUpdated;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class PresenceController extends AbstractController
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PresenceHandler
     */
    private $presenceHandler;
    /**
     * @var SearchHelper
     */
    private $searchHelper;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var ListingPresenceByMonth
     */
    private $listingPresenceByMonth;

    public function __construct(
        PresenceRepository $presenceRepository,
        JourRepository $jourRepository,
        PresenceHandler $presenceHandler,
        SearchHelper $searchHelper,
        ListingPresenceByMonth $listingPresenceByMonth
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->presenceHandler = $presenceHandler;
        $this->searchHelper = $searchHelper;
        $this->jourRepository = $jourRepository;
        $this->listingPresenceByMonth = $listingPresenceByMonth;
    }

    /**
     * @Route("/", name="mercredi_admin_presence_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchPresenceType::class);
        $form->handleRequest($request);
        $presences = $joursDto = [];
        $search = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::PRESENCE_LIST, $data);
            $search = true;
            $presences = $this->presenceRepository->search($data['nom'], $data['ecole'], $data['annee_scolaire']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/index.html.twig',
            [
                'presences' => $presences,
                'form' => $form->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * Liste toutes les presences par mois.
     *
     * @Route("/by/mois", name="mercredi_admin_presence_by_month", methods={"GET","POST"})
     *
     */
    public function indexByMonth(Request $request)
    {
        $search = false;
        $mois = null;
        $listingPresences = [];

        $form = $this->createForm(SearchPresenceByMonthType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $mois = $data['mois'];

            if (!$date = \DateTime::createFromFormat('d/m/Y', '01/'.$mois)) {
                $this->addFlash('danger', 'Mauvais format de date');

                return $this->redirectToRoute('mercredi_admin_presence_by_month');
            }

            $listingPresences = $this->listingPresenceByMonth->create($date);
            $this->searchHelper->saveSearch(SearchHelper::PRESENCE_LIST_BY_MONTH, $data);
            $search = true;
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/index_by_month.html.twig',
            [
                'form' => $form->createView(),
                'search_form' => $form->createView(),
                'search' => $search,
                'month' => $mois,
                'listingPresences' => $listingPresences,
            ]
        );
    }

    /**
     * @Route("/new/{tuteur}/{enfant}", name="mercredi_admin_presence_new", methods={"GET","POST"})
     * @Entity("tuteur", expr="repository.find(tuteur)")
     * @Entity("enfant", expr="repository.find(enfant)")
     */
    public function new(Request $request, Tuteur $tuteur, Enfant $enfant): Response
    {
        $dto = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($tuteur, $enfant, $days);

            $this->dispatchMessage(new PresenceCreated(1));

            return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/new.html.twig',
            [
                'enfant' => $enfant,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_presence_show", methods={"GET"})
     */
    public function show(Presence $presence): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/presence/show.html.twig',
            [
                'presence' => $presence,
                'enfant' => $presence->getEnfant(),
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_presence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Presence $presence): Response
    {
        $form = $this->createForm(PresenceType::class, $presence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->presenceRepository->flush();

            $this->dispatchMessage(new PresenceUpdated($presence->getId()));

            return $this->redirectToRoute('mercredi_admin_presence_show', ['id' => $presence->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/edit.html.twig',
            [
                'presence' => $presence,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_presence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Presence $presence): Response
    {
        if ($this->isCsrfTokenValid('delete'.$presence->getId(), $request->request->get('_token'))) {
            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatchMessage(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute('mercredi_admin_presence_index');
    }
}
