<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Calendar\DateProvider;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/presence')]
#[IsGranted('ROLE_MERCREDI_ECOLE')]
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

    #[Route(path: '/{dateSelected}', name: 'mercredi_ecole_presence_index')]
    public function default(\DateTime $dateSelected = null): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }

        if (!$dateSelected) {
            $dateSelected = Carbon::now()->toImmutable();
        }

        $accueils = $this->jourRepository->findDaysByMonth($dateSelected);
        $days = $this->dateProvider->daysOfMonth($dateSelected);
        $enfants = [];

        try {
            if (!$jour = $this->jourRepository->findOneByDate($dateSelected)) {
                $this->addFlash('danger', 'Il n\'y a pas d\'accueil ce '.$dateSelected->format('d-m-Y'));
            } else {
                $this->presenceRepository->findPresencesByJourAndEcoles($jour, $this->ecoles->toArray());
            }
        } catch (NonUniqueResultException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        $carbon = new CarbonImmutable($dateSelected);
        $next = $carbon->addMonth();
        $previous = $carbon->subMonth();
        $today = Carbon::today();
        $weeks = $this->dateProvider->weeksOfMonth($dateSelected);

        return $this->render('@AcMarcheMercrediEcole/presence/index.html.twig', [
            'days' => $days,
            'dateSelected' => $carbon,
            'next' => $next,
            'today' => $today,
            'weekdays' => $this->dateProvider->weekDaysName(),
            'previous' => $previous,
            'weeks' => $weeks,
            'enfants' => $enfants,
        ]);
    }

}
