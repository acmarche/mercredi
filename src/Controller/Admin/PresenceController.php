<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Presence\Form\PresenceNewType;
use AcMarche\Mercredi\Presence\Form\PresenceType;
use AcMarche\Mercredi\Presence\Form\SearchPresenceByMonthType;
use AcMarche\Mercredi\Presence\Form\SearchPresenceType;
use AcMarche\Mercredi\Presence\Message\PresenceCreated;
use AcMarche\Mercredi\Presence\Message\PresenceDeleted;
use AcMarche\Mercredi\Presence\Message\PresenceUpdated;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Utils\OrdreService;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Utils\DateUtils;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/presence')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class PresenceController extends AbstractController
{
    public function __construct(
        private PresenceRepository $presenceRepository,
        private PresenceHandlerInterface $presenceHandler,
        private SearchHelper $searchHelper,
        private ListingPresenceByMonth $listingPresenceByMonth,
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureHandlerInterface $factureHandler,
        private PresenceCalculatorInterface $presenceCalculator,
        private OrdreService $ordreService,
        private MessageBusInterface $dispatcher,
    ) {}

    #[Route(path: '/', name: 'mercredi_admin_presence_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $data = [];
        $displayRemarque = false;
        $jour = null;
        $form = $this->createForm(SearchPresenceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            /** @var Jour $jour */
            $jour = $dataForm['jour'];
            $displayRemarque = $dataForm['displayRemarque'];
            $ecole = $dataForm['ecole'];
            $this->searchHelper->saveSearch(SearchHelper::PRESENCE_LIST, $dataForm);
            $data = $this->presenceHandler->searchAndGrouping($jour, $ecole, $displayRemarque);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/index.html.twig',
            [
                'datas' => $data,
                'form' => $form,
                'search' => $form->isSubmitted(),
                'jour' => $jour,
                'display_remarques' => $displayRemarque,
            ],$response
        );
    }

    #[Route(path: '/by/month', name: 'mercredi_admin_presence_by_month', methods: ['GET', 'POST'])]
    public function indexByMonth(Request $request): Response
    {
        $mois = null;
        $listingPresences = [];
        $form = $this->createForm(SearchPresenceByMonthType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $mois = $data['mois'];
            $filter = $data['filter'];

            try {
                $date = DateUtils::createDateTimeFromDayMonth($mois);
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('mercredi_admin_presence_by_month');
            }

            $listingPresences = $this->listingPresenceByMonth->create($date, $filter);
            $this->searchHelper->saveSearch(SearchHelper::PRESENCE_LIST_BY_MONTH, $data);
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/index_by_month.html.twig',
            [
                'form' => $form,
                'search_form' => $form,
                'search' => $form->isSubmitted(),
                'month' => $mois,
                'listingPresences' => $listingPresences,
            ],$response
        );
    }

    #[Route(path: '/new/{tuteur}/{enfant}', name: 'mercredi_admin_presence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Tuteur $tuteur, Enfant $enfant): Response
    {
        $presenceSelectDays = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewType::class, $presenceSelectDays);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($tuteur, $enfant, $days);

            $this->dispatcher->dispatch(new PresenceCreated($days));

            return $this->redirectToRoute('mercredi_admin_enfant_show', [
                'id' => $enfant->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/new.html.twig',
            [
                'enfant' => $enfant,
                'tuteur' => $tuteur,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_presence_show', methods: ['GET'])]
    public function show(Presence $presence): Response
    {
        $facturePresence = $this->facturePresenceRepository->findByPresence($presence);
        $ordre = $this->presenceCalculator->getOrdreOnPresence($presence);
        $amount = $this->presenceCalculator->calculate($presence);
        $fratries = $this->ordreService->getFratriesPresents($presence);
        $plaine = $presence->getJour()->getPlaine();

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/show.html.twig',
            [
                'presence' => $presence,
                'facturePresence' => $facturePresence,
                'fratries' => $fratries,
                'ordre' => $ordre,
                'plaine' => $plaine,
                'amount' => $amount,
                'enfant' => $presence->getEnfant(),
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_presence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Presence $presence): Response
    {
        if ($this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PRESENCE)) {
            $this->addFlash('danger', 'Une présence déjà facturée ne peut être éditée');

            return $this->redirectToRoute('mercredi_admin_presence_show', [
                'id' => $presence->getId(),
            ]);
        }
        $form = $this->createForm(PresenceType::class, $presence);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->presenceRepository->flush();

            $this->dispatcher->dispatch(new PresenceUpdated($presence->getId()));

            return $this->redirectToRoute('mercredi_admin_presence_show', [
                'id' => $presence->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/edit.html.twig',
            [
                'presence' => $presence,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_presence_delete', methods: ['POST'])]
    public function delete(Request $request, Presence $presence): RedirectResponse
    {
        $enfant = $presence->getEnfant();
        if ($this->isCsrfTokenValid('delete'.$presence->getId(), $request->request->get('_token'))) {
            if ($this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PRESENCE)) {
                $this->addFlash('danger', 'Une présence déjà facturée ne peut être supprimée');

                return $this->redirectToRoute('mercredi_admin_presence_show', [
                    'id' => $presence->getId(),
                ]);
            }

            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatcher->dispatch(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_show', [
            'id' => $enfant->getId(),
        ]);
    }

    #[Route(path: '/non/payes', name: 'mercredi_admin_presence_non_payes', methods: ['POST', 'GET'])]
    public function nonPaye(): Response
    {
        $presences = PresenceUtils::groupByTuteur($this->presenceRepository->findWithOutPaiement());
        $presencesPlaines = PresenceUtils::groupByTuteur($this->presenceRepository->findWithOutPaiementPlaine());

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/non_paye.html.twig',
            [
                'presences' => $presences,
                'presencesPlaines' => $presencesPlaines,
            ],
        );
    }
}
