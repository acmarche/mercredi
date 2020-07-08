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
    use GetTuteurTrait;

    /**
     * @Route("/", name="mercredi_parent_attestation")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default(Request $request)
    {
        $this->hasTuteur();

        $relations = $this->relationRepository->findByTuteur($this->tuteur);
        $enfants = RelationUtils::extractEnfants($relations);

        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($this->tuteur);

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
