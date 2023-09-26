<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\OrdreService;
use AcMarche\Mercredi\Security\Checker\UserChecker;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/checkup')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class CheckupController extends AbstractController
{
    private $tutru = null;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private TuteurRepository $tuteurRepository,
        private UserRepository $userRepository,
        private PresenceRepository $presenceRepository,
        private OrdreService $ordreService,
        private JourRepository $jourRepository,
        private FactureRepository $factureRepository,
        private FactureCalculatorInterface $factureCalculator,
        private FacturePresenceRepository $facturePresenceRepository,
        private PresenceCalculatorInterface $presenceCalculator
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_checkup_index')]
    public function checkup(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/index.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/orphelin', name: 'mercredi_admin_checkup_orphelin')]
    public function orphelin(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/orphelins.html.twig',
            [
                'enfants' => $this->enfantRepository->findOrphelins(),
            ]
        );
    }

    #[Route(path: '/sansenfants', name: 'mercredi_admin_checkup_sansenfant')]
    public function sansenfant(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/sansenfants.html.twig',
            [
                'tuteurs' => $this->tuteurRepository->findSansEnfants(),
            ]
        );
    }

    #[Route(path: '/plantage', name: 'mercredi_admin_plantage')]
    public function plantage(): Response
    {
        $this->tutru->getAll();

        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/accounts', name: 'mercredi_admin_checkup_accounts')]
    public function accounts(): Response
    {
        $bad = [];
        $users = $this->userRepository->findAllOrderByNom();
        foreach ($users as $user) {
            $check = UserChecker::check($user);
            if (count($check) > 0) {
                $bad[] = $check;
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/accounts.html.twig',
            [
                'users' => $bad,
            ]
        );
    }

    #[Route(path: '/doublons', name: 'mercredi_admin_checkup_doublons')]
    public function doublon(): Response
    {
        $tuteurs = $this->tuteurRepository->findDoublon();
        $enfants = $this->enfantRepository->findDoublon();

        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/doublons.html.twig',
            [
                'tuteurs' => $tuteurs,
                'enfants' => $enfants,
            ]
        );
    }

    #[Route(path: '/presences')]
    public function presences(): Response
    {
        $dateTime = new DateTime('01-10-2021');
        $jours = $this->jourRepository->findDaysByMonth($dateTime, null);
        $presences = $this->presenceRepository->findByDays($jours);
        foreach ($presences as $presence) {
            $ordre = $this->ordreService->getOrdreOnPresence($presence);
            $presence->ordreTmp = $ordre;
            $presence->fratries = $this->ordreService->getFratriesPresents($presence);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/presences.html.twig',
            [
                'presences' => $presences,
            ]
        );
    }

    #[Route(path: '/factures')]
    public function factures(): Response
    {
        $factures = $this->factureRepository->findFacturesByMonth('10-2021');
        $total = 0;
        $data = [];
        $i = 0;
        foreach ($factures as $facture) {
            $tuteur = $facture->getTuteur();
            $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
                $facture,
                FactureInterface::OBJECT_PRESENCE
            );
            foreach ($facturePresences as $presenceFactured) {
                $presence = $this->presenceRepository->find($presenceFactured->getPresenceId());
                if (null !== $presence) {
                    $ordre = $this->presenceCalculator->getOrdreOnPresence($presence);
                    $prix = $this->presenceCalculator->getPrixByOrdre($presence, $ordre);
                }
                $prixFactured = $presenceFactured->getCoutBrut();
                $ordreFactured = $presenceFactured->getOrdre();
                if ($prix !== $prixFactured) {
                    $newcout = 0;
                    $data[$i]['tuteur'] = $tuteur;
                    $data[$i]['facture'] = $facture;
                    $data[$i]['presences'][] = [
                        'object' => $presence,
                        'prix' => 'Passe de '.$prixFactured.' € à '.$prix.' €',
                        'ordre' => 'Passe de '.$ordreFactured.' à '.$ordre,
                    ];
                    if (null !== $presence) {
                        $newcout = $this->presenceCalculator->calculate(
                            $presence
                        );
                    }
                    if (!isset($data[$i]['montant'])) {
                        $data[$i]['montant'] = 0;
                    }
                    $data[$i]['montant'] += ($newcout - $presenceFactured->getCoutCalculated());
                }
            }

            $facture->factureDetailDto = $this->factureCalculator->createDetail($facture);
            $total += $facture->factureDetailDto->total;
            ++$i;
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/factures.html.twig',
            [
                'factures' => $factures,
                'total' => $total,
                'data' => $data,
            ]
        );
    }
}
