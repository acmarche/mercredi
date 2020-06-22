<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 * @Route("/tuteur")
 */
class TuteurController extends AbstractController
{
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(TuteurRepository $tuteurRepository)
    {
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * @Route("/", name="mercredi_parent_tuteur_show", methods={"GET"})
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function show()
    {
        $user = $this->getUser();
        $tuteurs = $user->getTuteurs();

        if (0 == count($tuteurs)) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        $tuteur = $tuteurs[0];
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/tuteur/show.html.twig',
            [
                'tuteurIsComplete' => $tuteurIsComplete,
                'tuteur' => $tuteur,
            ]
        );
    }

    /**
     * @Route("/edit", name="mercredi_parent_tuteur_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function edit(Request $request)
    {
        $user = $this->getUser();
        $tuteurs = $user->getTuteurs();

        if (0 == count($tuteurs)) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        $tuteur = $tuteurs[0];

        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurUpdated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_parent_tuteur_show');
        }

        return $this->render(
            '@AcMarcheMercrediParent/tuteur/edit.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }
}
