<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/ajax")
 */
final class AjaxController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(EnfantRepository $enfantRepository, TuteurRepository $tuteurRepository)
    {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * @Route("/tuteurs/{keyword}", name="mercredi_admin_ajax_tuteurs")
     * @param string|null $keyword
     * @return JsonResponse
     */
    public function tuteurs(?string $keyword = null): JsonResponse
    {
        $tuteurs = $data = [];
        if ($keyword) {
            $tuteurs = $this->tuteurRepository->search($keyword);
        }
        $i = 0;
        foreach ($tuteurs as $tuteur) {
            $data[$i]['id'] = $tuteur->getId();
            $data[$i]['nom'] = $tuteur->getNom().' '.$tuteur->getPrenom();
            ++$i;
        }

        return $this->json($data);
    }

    /**
     * @Route("/enfants/{keyword}", name="mercredi_admin_ajax_enfants")
     * @param string|null $keyword
     * @return JsonResponse
     */
    public function enfants(?string $keyword = null): JsonResponse
    {
        $enfants = $data = [];
        if ($keyword) {
            $enfants = $this->enfantRepository->findByName($keyword);
        }

        $i = 0;
        foreach ($enfants as $enfant) {
            $data[$i]['id'] = $enfant->getId();
            $data[$i]['nom'] = $enfant->getNom();
            $data[$i]['prenom'] = $enfant->getPrenom();
            $data[$i]['value'] = $enfant->getNom().' '.$enfant->getPrenom();
            $data[$i]['label'] = $enfant->getNom().' '.$enfant->getPrenom();
            $birthday = '';
            if ($enfant->getBirthday() !== null) {
                $birthday = $enfant->getBirthday()->format('d-m-Y');
            }
            $data[$i]['birthday'] = $birthday;
            ++$i;
        }

        return $this->json($data);
    }
}
