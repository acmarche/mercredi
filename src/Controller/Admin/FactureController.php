<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Contrat\Facture\FactureRenderInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Form\FactureManualType;
use AcMarche\Mercredi\Facture\Form\FacturePayerType;
use AcMarche\Mercredi\Facture\Form\FactureSearchByEcoleType;
use AcMarche\Mercredi\Facture\Form\FactureSearchType;
use AcMarche\Mercredi\Facture\Form\FactureSelectMonthType;
use AcMarche\Mercredi\Facture\Message\FactureCreated;
use AcMarche\Mercredi\Facture\Message\FactureDeleted;
use AcMarche\Mercredi\Facture\Message\FacturesCreated;
use AcMarche\Mercredi\Facture\Message\FactureUnpaided;
use AcMarche\Mercredi\Facture\Message\FactureUpdated;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\QrCode\QrCodeGenerator;
use Exception;
use Knp\DoctrineBehaviors\Exception\ShouldNotHappenException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture')]
final class FactureController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository,
        private FactureHandlerInterface $factureHandler,
        private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository,
        private FactureCalculatorInterface $factureCalculator,
        private FactureRenderInterface $factureRender,
        private MessageBusInterface $dispatcher,
        private QrCodeGenerator $qrCodeGenerator,
    ) {
    }

    #[Route(path: '/{id}/index', name: 'mercredi_admin_facture_index_by_tuteur', methods: ['GET', 'POST'])]
    public function indexByTuteur(Tuteur $tuteur): Response
    {
        $factures = $this->factureRepository->findByTuteur($tuteur);
        $form = $this->createForm(
            FactureSelectMonthType::class,
            null,
            [
                'action' => $this->generateUrl('mercredi_admin_facture_new_month', [
                    'id' => $tuteur->getId(),
                ]),
            ],
        );

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/search', name: 'mercredi_admin_facture_index', methods: ['GET', 'POST'])]
    public function search(Request $request): Response
    {
        $factures = [];
        $form = $this->createForm(FactureSearchType::class);
        $form->handleRequest($request);
        $total = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $factures = $this->factureRepository->search(
                $dataForm['numero'],
                $dataForm['tuteur'],
                $dataForm['enfant'],
                $dataForm['ecole'],
                $dataForm['plaine'],
                $dataForm['paye'],
                $dataForm['datePaiement'],
                $dataForm['mois'],
                $dataForm['communication'],
            );
        }
        foreach ($factures as $facture) {
            $facture->factureDetailDto = $this->factureCalculator->createDetail($facture);
            $total += $facture->factureDetailDto->total;
        }
        $formMonth = $this->createForm(
            FactureSelectMonthType::class,
            null,
            [
                'action' => $this->generateUrl('mercredi_admin_facture_new_month_all'),
            ],
        );

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/search.html.twig',
            [
                'factures' => $factures,
                'form' => $form->createView(),
                'formMonth' => $formMonth->createView(),
                'search' => $form->isSubmitted(),
                'total' => $total,
            ],
        );
    }

    #[Route(path: '/{id}/manual', name: 'mercredi_admin_facture_new_manual', methods: ['GET', 'POST'])]
    public function newManual(Request $request, Tuteur $tuteur): Response
    {
        $facture = $this->factureHandler->newFacture($tuteur);
        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur);
        $accueils = $this->facturePresenceNonPayeRepository->findAccueilsNonPayes($tuteur);
        $form = $this->createForm(FactureManualType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = $request->request->all('presences');
            $accueilsF = $request->request->all('accueils');
            $this->factureHandler->handleManually($facture, $presencesF, $accueilsF);

            $this->dispatcher->dispatch(new FactureCreated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/new.html.twig',
            [
                'tuteur' => $tuteur,
                'presences' => $presences,
                'accueils' => $accueils,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/month/', name: 'mercredi_admin_facture_new_month', methods: ['GET', 'POST'])]
    public function newByMonth(Request $request, Tuteur $tuteur): RedirectResponse
    {
        $form = $this->createForm(FactureSelectMonthType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $month = $form->get('mois')->getData();

            if (($facture = $this->factureHandler->generateByMonthForTuteur($tuteur, $month)) === null) {
                $this->addFlash('warning', 'Aucune présences ou accueils non facturés pour ce mois');

                return $this->redirectToRoute('mercredi_admin_facture_index_by_tuteur', [
                    'id' => $tuteur->getId(),
                ]);
            }

            $this->dispatcher->dispatch(new FactureCreated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }
        $this->addFlash('danger', 'Date non valide');

        return $this->redirectToRoute('mercredi_admin_facture_index_by_tuteur', [
            'id' => $tuteur->getId(),
        ]);
    }

    #[Route(path: '/for/all/', name: 'mercredi_admin_facture_new_month_all', methods: ['GET', 'POST'])]
    public function newByMonthForAll(Request $request): Response
    {
        $form = $this->createForm(FactureSelectMonthType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $month = $form->get('mois')->getData();

            try {
                $factures = $this->factureHandler->generateByMonthForEveryone($month);
            } catch (Exception $exception) {
                $this->addFlash('danger', 'Erreur survenue: '.$exception->getMessage());

                return $this->redirectToRoute('mercredi_admin_facture_new_month_all');
            }

            if ([] === $factures) {
                $this->addFlash('warning', 'Aucune présences ou accueils non facturés pour ce mois');

                return $this->redirectToRoute('mercredi_admin_facture_new_month_all');
            }

            $this->dispatcher->dispatch(new FacturesCreated($factures));

            return $this->redirectToRoute('mercredi_admin_facture_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/generate.html.twig',
            [
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/show', name: 'mercredi_admin_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $html = $this->factureRender->render($facture);

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/show.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'content' => $html,
            ],
        );
    }

    #[Route(path: '/{id}/qrcode', name: 'mercredi_admin_facture_qrcode', methods: ['GET'])]
    public function qrcode(Facture $facture): Response
    {
        $dto = $this->factureCalculator->createDetail($facture);
        $img = null;
        try {
            $img = $this->qrCodeGenerator->generateForFacture($facture, $dto->total);
        } catch (ShouldNotHappenException|Exception $e) {
            $this->addFlash('danger', 'erreur image qrcode '.$e->getMessage());
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/qrcode.html.twig',
            [
                'facture' => $facture,
                'img' => $img,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureEditType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureRepository->flush();

            $this->dispatcher->dispatch(new FactureUpdated($facture->getId()));

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/edit.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{uuid}/payer', name: 'mercredi_admin_facture_payer', methods: ['GET', 'POST'])]
    public function payer(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FacturePayerType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureRepository->flush();

            $this->addFlash('success', 'Facture payée');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/payer.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture): RedirectResponse
    {
        $tuteur = null;
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $factureId = $facture->getId();
            $tuteur = $facture->getTuteur();
            $this->factureRepository->remove($facture);
            $this->factureRepository->flush();
            $this->dispatcher->dispatch(new FactureDeleted($factureId));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_show', [
            'id' => $tuteur->getId(),
        ]);
    }

    #[Route(path: '/{id}/unpaid', name: 'mercredi_admin_facture_unpaid', methods: ['POST'])]
    public function unpaid(Request $request, Facture $facture): RedirectResponse
    {
        if ($this->isCsrfTokenValid('unpaid'.$facture->getId(), $request->request->get('_token'))) {
            $factureId = $facture->getId();
            $facture->setPayeLe(null);
            $this->factureRepository->flush();
            $this->dispatcher->dispatch(new FactureUnpaided($factureId));
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }

    #[Route(path: '/byecole', name: 'mercredi_admin_facture_by_ecole', methods: ['GET', 'POST'])]
    public function byEcoles(Request $request): Response
    {
        $factures = [];
        $form = $this->createForm(FactureSearchByEcoleType::class);
        $form->handleRequest($request);
        $total = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $factures = $this->factureRepository->byEcoleAndMonth(
                $dataForm['ecole'],
                $dataForm['mois'],
            );
        }
        foreach ($factures as $facture) {
            $facture->factureDetailDto = $this->factureCalculator->createDetail($facture);
            $total += $facture->factureDetailDto->total;
        }

        $group = [];
        $totalGroup = 0;
        foreach ($factures as $facture) {
            if (!isset($group[$facture->getEcoles()])) {
                $group[$facture->getEcoles()] = 0;
            }
            $group[$facture->getEcoles()] += $facture->factureDetailDto->total;
            $totalGroup += $facture->factureDetailDto->total;
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/by_ecole.html.twig',
            [
                'factures' => $factures,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
                'total' => $total,
                'group' => $group,
                'totalGroup' => $totalGroup,
            ],
        );
    }
}
