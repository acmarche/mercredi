<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Contrat\AccueilInterface;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Utils\DateUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/stats')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class StatController extends AbstractController
{
    public function __construct(
        private AccueilRepository $accueilRepository,
        private PresenceRepository $presenceRepository,
        private EcoleRepository $ecoleRepository
    ) {
    }

    #[Route(path: '/{year}', name: 'mercredi_admin_stat_index')]
    public function default(int $year = 2023): Response
    {
        $years = range(date('Y') - 2, date('Y') + 1);
        $months = DateUtils::getMonthsOfYear($year);
        $ecoles = $this->ecoleRepository->findAllOrderByNom();
        $data = [];
        foreach ($months as $month) {
            $monthKey = $month->format('Y-m');
            $data[$monthKey] = [];

            foreach ($ecoles as $ecole) {
                $data[$monthKey][$ecole->getId()]['ecole'] = $ecole;
                $presences = $this->presenceRepository->findByEcoleAndMonth($ecole, $month);
                $data[$monthKey][$ecole->getId()]['presences'] = $presences;
                foreach (AccueilInterface::HEURES as $heure => $name) {
                    $countHours = 0;
                    $retard = 0;
                    $accueils = $this->accueilRepository->findByMonthHeureAndEcole($month, $heure, $ecole);
                    foreach ($accueils as $accueil) {
                        $countHours += $accueil->getDuree();
                        if ($accueil->getHeureRetard()) {
                            $retard++;
                        }
                    }
                    $data[$monthKey][$ecole->getId()]['accueils'][$heure] = [
                        'accueils' => $accueils,
                        'countHours' => $countHours,
                        'retard' => $retard,
                    ];
                }
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/stat/index.html.twig',
            [
                'data' => $data,
                'years' => $years,
                'yearSelected' => $year,
            ]
        );
    }
}
