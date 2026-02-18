<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Contrat\AccueilInterface;
use AcMarche\Mercredi\Accueil\Form\SearchAccueilForQuarter;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Utils\DateUtils;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/presence/one')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class PresenceOneController extends AbstractController
{
    public function __construct(
        private AccueilRepository $accueilRepository,
    ) {
    }

    /**
     * Liste toutes les presences par trimestre
     */
    #[Route(path: '/quarter', name: 'mercredi_admin_presence_by_quarter', methods: ['GET', 'POST'])]
    public function indexByQuarter(Request $request): Response
    {
        $form = $this->createForm(SearchAccueilForQuarter::class, ['year' => date('Y')]);
        $form->handleRequest($request);
        $childs = $data = $ages = $averages = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $ecole = $dataForm['ecole'];
            $trimestre = $dataForm['trimestre'];
            $year = $dataForm['year'];

            $months = match ($trimestre) {
                1 => [1, 2, 3],
                2 => [4, 5, 6],
                3 => [7, 8, 9],
                4 => [10, 11, 12],
                default => [],
            };

            $totalAccueilsMatin = 0;
            $totalAccueilsSoir = 0;
            $totalDureeMatin = 0;
            $totalDureeSoir = 0;
            $totalDaysWithData = 0;

            foreach ($months as $monthString) {
                try {
                    $month = DateUtils::createDateTimeFromDayMonth($monthString.'/'.$year);
                    $dataMonth = ['days' => []];
                    $totalByMonthMatin = 0;
                    $totalByMonthSoir = 0;
                    $totalDureeByMonthMatin = 0;
                    $totalDureeByMonthSoir = 0;

                    foreach (DateUtils::getAllDaysOfMonth($month) as $day) {
                        if (DateUtils::dayIsWeek($day)) {
                            $dayKey = $day->format('Y-m-d');
                            $dataMonth['days'][$dayKey] = [];

                            foreach (AccueilInterface::HEURES as $heure => $name) {
                                $accueils = $this->accueilRepository->findByDateHeureAndEcole(
                                    $day,
                                    $heure,
                                    $ecole,
                                );
                                $count = count($accueils);
                                $duree = 0;
                                foreach ($accueils as $accueil) {
                                    $duree += $accueil->getDuree();
                                    $enfant = $accueil->getEnfant();
                                    $childs[$enfant->getId()] = $enfant;
                                }
                                $dataMonth['days'][$dayKey][$heure] = [
                                    'count' => $count,
                                    'duree' => $duree,
                                ];

                                if ($heure === AccueilInterface::MATIN) {
                                    $totalByMonthMatin += $count;
                                    $totalDureeByMonthMatin += $duree;
                                } else {
                                    $totalByMonthSoir += $count;
                                    $totalDureeByMonthSoir += $duree;
                                }
                            }

                            $totalDaysWithData++;
                        }
                    }

                    $dataMonth['totalMatin'] = $totalByMonthMatin;
                    $dataMonth['totalSoir'] = $totalByMonthSoir;
                    $dataMonth['totalDureeMatin'] = $totalDureeByMonthMatin;
                    $dataMonth['totalDureeSoir'] = $totalDureeByMonthSoir;
                    $data[$month->format('Y-m-d')] = $dataMonth;

                    $totalAccueilsMatin += $totalByMonthMatin;
                    $totalAccueilsSoir += $totalByMonthSoir;
                    $totalDureeMatin += $totalDureeByMonthMatin;
                    $totalDureeSoir += $totalDureeByMonthSoir;
                } catch (Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }

            $averages = [
                'matin' => [
                    'frequentation' => $totalDaysWithData > 0 ? round($totalAccueilsMatin / $totalDaysWithData, 2) : 0,
                    'duree' => $totalAccueilsMatin > 0 ? round($totalDureeMatin / $totalAccueilsMatin, 2) : 0,
                    'total' => $totalAccueilsMatin,
                    'totalDuree' => $totalDureeMatin,
                ],
                'soir' => [
                    'frequentation' => $totalDaysWithData > 0 ? round($totalAccueilsSoir / $totalDaysWithData, 2) : 0,
                    'duree' => $totalAccueilsSoir > 0 ? round($totalDureeSoir / $totalAccueilsSoir, 2) : 0,
                    'total' => $totalAccueilsSoir,
                    'totalDuree' => $totalDureeSoir,
                ],
                'days' => $totalDaysWithData,
            ];

            $ages = [
                'all' => count($childs),
                'mat' => 0,
                'prim' => 0,
            ];

            $ref = DateUtils::createDateTimeFromDayMonth($months[0].'/'.$year);
            foreach ($childs as $child) {
                if ($child->getAge($ref) > 6) {
                    $ages['prim']++;
                } else {
                    $ages['mat']++;
                }
            }
        }

        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);

        return $this->render(
            '@AcMarcheMercrediAdmin/presence/index_by_quarter.html.twig',
            [
                'form' => $form,
                'data' => $data,
                'search' => $form->isSubmitted(),
                'ages' => $ages,
                'averages' => $averages,
            ],
            $response
        );
    }

}
