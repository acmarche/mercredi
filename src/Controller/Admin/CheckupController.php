<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Repository\UserRepository;
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
    private EnfantRepository $enfantRepository;
    private TuteurRepository $tuteurRepository;
    private UserRepository $userRepository;
    private $tutru = null;

    public function __construct(
        EnfantRepository $enfantRepository,
        TuteurRepository $tuteurRepository,
        UserRepository $userRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_checkup_index")
     */
    public function checkup(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/index.html.twig',
            [

            ]
        );
    }

    /**
     * @Route("/orphelin", name="mercredi_admin_checkup_orphelin")
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
     * @Route("/sansenfants", name="mercredi_admin_checkup_sansenfant")
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

    /**
     * @Route("/accounts", name="mercredi_admin_checkup_accounts")
     */
    public function accounts(): Response
    {
        $bad = [];
        $users = $this->userRepository->findAllOrderByNom();
        foreach ($users as $user) {
            if ($user->getRoles() < 1) {
                $bad[] = ['error' => 'Aucun rôle', 'user' => $user];
                continue;
            }
            if ($user->hasRole(MercrediSecurityRole::ROLE_PARENT)) {
                if (count($user->getTuteurs()) === 0) {
                    $bad[] = ['error' => 'Rôle parent, mais aucun parent associé', 'user' => $user];
                    continue;
                }
            }
            if ($user->hasRole(MercrediSecurityRole::ROLE_ANIMATEUR)) {
                if (count($user->getAnimateurs()) === 0) {
                    $bad[] = ['error' => 'Rôle animateur, mais aucun animateur associé', 'user' => $user];
                    continue;
                }
            }
            if ($user->hasRole(MercrediSecurityRole::ROLE_ECOLE)) {
                if (count($user->getEcoles()) === 0) {
                    $bad[] = ['error' => 'Rôle école, mais aucune école associée', 'user' => $user];
                    continue;
                }
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/checkup/accounts.html.twig',
            [
                'users' => $bad,
            ]
        );
    }

    /**
     * @Route("/doublons", name="mercredi_admin_checkup_doublons")
     */
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
}
