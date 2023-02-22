<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Contrat\AccueilInterface;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Search\Form\SearchNameType;
use AcMarche\Mercredi\Utils\DateUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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

    #[Route(path: '/', name: 'mercredi_admin_stat_index')]
    public function default(): Response
    {
        $form = $this->createForm(SearchNameType::class);
        $year = 2023;
        $months = DateUtils::getMonthsOfYear($year);
        $ecoles = $this->ecoleRepository->findAllOrderByNom();
        $data = [];
        foreach ($months as $month) {
            $monthKey = $month->format('Y-m');
            $data[$monthKey] = [];
            foreach ($ecoles as $ecole) {
                $data[$monthKey][$ecole->getId()]['ecole'] = $ecole;
                foreach (AccueilInterface::HEURES as $heure => $name) {
                    $accueils = $this->accueilRepository->findByMonthHeureAndEcole($month, $heure, $ecole);
                    $data[$monthKey][$ecole->getId()]['accueils'][$heure] = $accueils;
                }
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/stat/index.html.twig',
            [
                'data' => $data,
                'form' => $form->createView(),
            ]
        );
    }
}
