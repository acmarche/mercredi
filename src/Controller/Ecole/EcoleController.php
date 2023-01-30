<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Contrat\AccueilInterface;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use Carbon\Carbon;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/ecole')]
#[IsGranted( 'ROLE_MERCREDI_ECOLE')]
final class EcoleController extends AbstractController
{
    use GetEcolesTrait;

    public function __construct(
        private EcoleRepository $ecoleRepository,
        private EnfantRepository $enfantRepository
    ) {
    }

    #[Route(path: '/', name: 'mercredi_ecole_ecole_index', methods: ['GET'])]
    public function index(): Response
    {
        if (($response = $this->hasEcoles()) !== null) {
            return $response;
        }
        $today = Carbon::today();

        return $this->render(
            '@AcMarcheMercrediEcole/ecole/index.html.twig',
            [
                'ecoles' => $this->ecoles,
                'today' => $today,
                'heures' => AccueilInterface::HEURES,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_ecole_ecole_show', methods: ['GET'])]
    #[IsGranted( 'ecole_show', subject: 'ecole')]
    public function show(Ecole $ecole): Response
    {
        $enfants = $this->enfantRepository->findByEcolesForEcole([$ecole]);
        $today = Carbon::today();

        return $this->render(
            '@AcMarcheMercrediEcole/ecole/show.html.twig',
            [
                'ecole' => $ecole,
                'enfants' => $enfants,
                'today' => $today,
                'heures' => AccueilInterface::HEURES,
            ]
        );
    }
}
