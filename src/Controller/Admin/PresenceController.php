<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Calculator\PresenceCalculatorInterface;
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
use AcMarche\Mercredi\Utils\DateUtils;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/presence")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class PresenceController extends AbstractController
{
    private PresenceRepository $presenceRepository;
    private PresenceHandler $presenceHandler;
    private SearchHelper $searchHelper;
    private ListingPresenceByMonth $listingPresenceByMonth;
    private PresenceCalculatorInterface $presenceCalculator;
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureHandler $factureHandler;

    public function __construct(
        PresenceRepository $presenceRepository,
        PresenceHandler $presenceHandler,
        SearchHelper $searchHelper,
        ListingPresenceByMonth $listingPresenceByMonth,
        PresenceCalculatorInterface $presenceCalculator,
        FacturePresenceRepository $facturePresenceRepository,
        FactureHandler $factureHandler
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->presenceHandler = $presenceHandler;
        $this->searchHelper = $searchHelper;
        $this->listingPresenceByMonth = $listingPresenceByMonth;
        $this->presenceCalculator = $presenceCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureHandler = $factureHandler;
    }

    /**
     * @Route("/", name="mercredi_admin_presence_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchPresenceType::class);
        $form->handleRequest($request);
        $data = [];
        $search = $displayRemarque = false;
        $jour = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            /** @var Jour $jour */
            $jour = $dataForm['jour'];
            $displayRemarque = $dataForm['displayRemarque'];
            $this->searchHelper->saveSearch(SearchHelper::PRESENCE_LIST, $dataForm);
            $search = true;
            $data = $this->presenceHandler->handleForGrouping($jour, $dataForm['ecole'], $displayRemarque);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/index.html.twig',
            [
                'data' => $data,
                'form' => $form->createView(),
                'search' => $search,
                'jour' => $jour,
                'display_remarques' => $displayRemarque,
            ]
        );
    }

    /**
     * Liste toutes les presences par mois.
     *
     * @Route("/by/month", name="mercredi_admin_presence_by_month", methods={"GET","POST"})
     */
    public function indexByMonth(Request $request): Response
    {
        $search = false;
        $mois = null;
        $listingPresences = [];

        $form = $this->createForm(SearchPresenceByMonthType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $mois = $data['mois'];

            try {
                $date = DateUtils::createDateTimeFromDayMonth($mois);
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());

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
        $presenceSelectDays = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewType::class, $presenceSelectDays);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($tuteur, $enfant, $days);

            $this->dispatchMessage(new PresenceCreated($days));

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
        $cout = $this->presenceCalculator->calculate($presence);
        $facturePresence = $this->facturePresenceRepository->findByPresence($presence);

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/show.html.twig',
            [
                'presence' => $presence,
                'facturePresence' => $facturePresence,
                'cout' => $cout,
                'enfant' => $presence->getEnfant(),
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_presence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Presence $presence): Response
    {
        if ($this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PRESENCE)) {
            $this->addFlash('danger', 'Une présence déjà facturée ne peut être éditée');

            return $this->redirectToRoute('mercredi_admin_presence_show', ['id' => $presence->getId()]);
        }

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
     * @Route("/{id}/delete", name="mercredi_admin_presence_delete", methods={"POST"})
     */
    public function delete(Request $request, Presence $presence): Response
    {
        if ($this->isCsrfTokenValid('delete'.$presence->getId(), $request->request->get('_token'))) {

            if ($this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PRESENCE)) {
                $this->addFlash('danger', 'Une présence déjà facturée ne peut être supprimée');

                return $this->redirectToRoute('mercredi_admin_presence_show', ['id' => $presence->getId()]);
            }

            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatchMessage(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute('mercredi_admin_presence_index');
    }
}
