<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AjaxController extends AbstractController
{
    public function __construct(
        private AccueilRepository $accueilRepository,
        private EnfantRepository $enfantRepository,
    ) {}

    #[Route(path: '/accueil/ajax/duree', name: 'mercredi_ecole_ajax_duree', methods: ['POST'])]
    #[IsGranted('ROLE_MERCREDI_ECOLE')]
    public function updateDuree(Request $request): Response
    {
        $data = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $enfantId = $data->enfantId;
        $date = $data->date;
        $heure = $data->heure;
        $duree = $data->duree;
        if (($enfant = $this->enfantRepository->find($enfantId)) === null) {
            return $this->json([
                'error' => 'Enfant non trouvé',
            ]);
        }
        $accueil = $this->accueilRepository->findByEnfantDateAndHeure($enfant, $date, $heure);

        return $this->json($data);
    }
}
