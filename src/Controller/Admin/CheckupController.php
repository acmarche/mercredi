<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/checkup")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class CheckupController extends AbstractController
{
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    private $tutru;

    public function __construct(EnfantRepository $enfantRepository, TuteurRepository $tuteurRepository)
    {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * @Route("/orphelin", name="mercredi_admin_orphelin")
     */
    public function orphelin(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/orphelins.html.twig',
            [
                'enfants' => $this->enfantRepository->findOrphelins(),
            ]
        );
    }

    /**
     * @Route("/sansenfants", name="mercredi_admin_sansenfant")
     */
    public function sansenfant(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/sansenfants.html.twig',
            [
                'tuteurs' => $this->tuteurRepository->findSansEnfants(),
            ]
        );
    }

    /**
     * @Route("/plantage", name="mercredi_admin_plantage")
     */
    public function plantage(): Response
    {
        $this->tutru->getAll();

        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [

            ]
        );
    }
}
