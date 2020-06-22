<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/attestation")
 */
class AttestationController extends AbstractController
{
    public function __construct()
    {

    }

    /**
     * @Route("/", name="mercredi_parent_attestation")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default(Request $request)
    {
        $user = $this->getUser();
        $tuteurs = $user->getTuteurs();

        if (0 == count($tuteurs)) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        $tuteur = $tuteurs[0];

        $relations = $this->relationRepository->findByTuteur($tuteur);
        $enfants = RelationUtils::extractEnfants($relations);

        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/default/index.html.twig',
            [
                'enfants' => $enfants,
                'tuteurIsComplete' => $tuteurIsComplete,
                'year' => date('Y'),
            ]
        );
    }
}
