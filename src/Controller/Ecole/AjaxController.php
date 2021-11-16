<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    private AccueilRepository $accueilRepository;
    private EnfantRepository $enfantRepository;

    public function __construct(AccueilRepository $accueilRepository, EnfantRepository $enfantRepository)
    {
        $this->accueilRepository = $accueilRepository;
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @Route("/accueil/ajax/duree", name="mercredi_ecole_ajax_duree", methods={"POST"})
     * @IsGranted("ROLE_MERCREDI_ECOLE")
     */
    public function updateDuree(Request $request): Response
    {
        $data = json_decode($request->getContent());
        $enfantId = $data->enfantId;
        $date = $data->date;
        $heure = $data->heure;
        $duree = $data->duree;

        if(!$enfant = $this->enfantRepository->find($enfantId)) {
            return $this->json(['error'=>'Enfant non trouvé']);
        }

        $accueil = $this->accueilRepository->findByEnfantDateAndHeure($enfant, $date, $heure);

        return $this->json($data);

        return $this->json('<div class="alert alert-success">Tri enregistré</div>');
    }
}
