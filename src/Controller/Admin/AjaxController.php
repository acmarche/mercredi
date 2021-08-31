<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/ajax")
 */
final class AjaxController extends AbstractController
{
    private EnfantRepository $enfantRepository;
    private TuteurRepository $tuteurRepository;

    public function __construct(EnfantRepository $enfantRepository, TuteurRepository $tuteurRepository)
    {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * not use
     * @Route("/tuteurs", name="mercredi_admin_ajax_tuteurs")
     */
    public function tuteurs(Request $request): Response
    {
        $keyword = $request->get('q');
        $tuteurs = [];
        if ($keyword) {
            $tuteurs = $this->tuteurRepository->search($keyword);
        }

        return $this->render('@AcMarcheMercredi/commun/tuteur/_list.html.twig', ['tuteurs' => $tuteurs]);
    }

    /**
     * @Route("/enfants/link", name="mercredi_admin_ajax_enfants", methods={"GET"})
     */
    public function enfants(Request $request): Response
    {
        $keyword = $request->get('q');
        $enfants = [];
        if ($keyword) {
            $enfants = $this->enfantRepository->findByName($keyword, true, 10);
        }

        return $this->render('@AcMarcheMercredi/commun/enfant/_list.html.twig', ['enfants' => $enfants]);
    }

    /**
     * @Route("/enfants/nolink", name="mercredi_admin_ajax_enfants_no_link", methods={"GET"})
     */
    public function enfantsNoLink(Request $request): Response
    {
        $keyword = $request->get('q');
        $enfants = [];
        if ($keyword) {
            $enfants = $this->enfantRepository->findByName($keyword, true, 10);
        }

        return $this->render('@AcMarcheMercredi/commun/enfant/_list_not_link.html.twig', ['enfants' => $enfants]);
    }

    /**
     * not use
     * @Route("/plaine/date", name="mercredi_admin_ajax_plaine_new_date")
     */
    public function plaineDate(Request $request): Response
    {
        $index = $request->get('index',0);

        return $this->render('@AcMarcheMercrediAdmin/plaine/_new_line.html.twig', ['index'=>$index]);
    }

}
