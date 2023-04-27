<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Calendar\DateProvider;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/presence')]
final class PresenceController extends AbstractController
{
    use GetEcolesTrait;
    use OrganisationPropertyInitTrait;

    public function __construct(
        private JourRepository $jourRepository,
        private PresenceRepository $presenceRepository,
        private DateProvider $dateProvider,
    ) {
    }

    #[Route(path: '/{yearmonth}', name: 'mercredi_ecole_presence_index')]
    #[IsGranted('ROLE_MERCREDI_ECOLE')]
    public function default(string $yearmonth = null): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }
        $year = date('Y');
        $currentMonth = new \DateTime();
        $days = $this->jourRepository->findDaysByMonth($currentMonth);
        $dateSelected = Carbon::now()->toImmutable();
        if ($yearmonth) {
            $dateSelected = $this->dateProvider->createDateFromYearMonth($yearmonth);
        } else {
            $yearmonth = $dateSelected->format('Y'.'-'.$dateSelected->month);
        }

        //$presences = $this->presenceRepository->findByMonthAndCategory($yearmonth);
        $days = $this->dateProvider->daysOfMonth($dateSelected);
        $data = [];
        foreach ($days as $day) {
            /*  $data[$day->day] = $this->presenceRepository->findPlanningByDayAndCategory(
                  $day
              );*/
        }
        $enfants = [];
        $next = $dateSelected->addMonth();
        $previous = $dateSelected->subMonth();
        $today = Carbon::today();

        $weeks = $this->dateProvider->weeksOfMonth($dateSelected);
        //$enfants = $this->enfantRepository->searchForEcole($this->ecoles, $nom, $accueil);
        //$presences = $this->presenceRepository->findWithoutPlaineByEnfant($enfant);

        return $this->render('@AcMarcheMercrediEcole/presence/index.html.twig', [
            'days' => $days,
            'dateSelected' => $dateSelected,
            'next' => $next,
            'today' => $today,
            'weekdays' => $this->dateProvider->weekDaysName(),
            'previous' => $previous,
            'weeks' => $weeks,
            'enfants' => $enfants,
        ]);
    }

}
