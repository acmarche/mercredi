<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Accueil\Contrat\AccueilInterface;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Ecole;
use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ecole")
 * @IsGranted("ROLE_MERCREDI_ECOLE")
 */
final class EcoleController extends AbstractController
{
    use GetEcolesTrait;

    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(EcoleRepository $ecoleRepository, EnfantRepository $enfantRepository)
    {
        $this->ecoleRepository = $ecoleRepository;
        $this->enfantRepository = $enfantRepository;
    }

    /**
     * @Route("/", name="mercredi_ecole_ecole_index", methods={"GET"})
     */
    public function index(): Response
    {
        if ($response = $this->hasEcoles()) {
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

    /**
     * @Route("/{id}", name="mercredi_ecole_ecole_show", methods={"GET"})
     * @IsGranted("ecole_show", subject="ecole")
     */
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
