<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Relation\Dto\TuteurEnfantDto;
use AcMarche\Mercredi\Relation\Form\TuteurEnfantQuickType;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Handler\AssociationHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/parent_enfant")
 */
final class QuickController extends AbstractController
{
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var AssociationHandler
     */
    private $associationHandler;

    public function __construct(
        TuteurRepository $tuteurRepository,
        EnfantRepository $enfantRepository,
        RelationRepository $relationRepository,
        AssociationHandler $associationHandler
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->enfantRepository = $enfantRepository;
        $this->relationRepository = $relationRepository;
        $this->associationHandler = $associationHandler;
    }

    /**
     * @Route("/", name="mercredi_admin_parent_enfant_new")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(TuteurEnfantQuickType::class, new TuteurEnfantDto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tuteur = $form->getData()->getTuteur();
            $enfant = $form->getData()->getEnfant();

            $this->tuteurRepository->persist($tuteur);
            $this->tuteurRepository->flush();
            $this->enfantRepository->persist($enfant);
            $this->enfantRepository->flush();

            $relation = new Relation($tuteur, $enfant);

            $this->relationRepository->persist($relation);
            $this->relationRepository->flush();
            $user = $password = null;

            if ($tuteur->getEmail()) {
                $user = $this->associationHandler->handleCreateUserFromTuteur($tuteur);
                $password = $user->getPlainPassword();
                $this->addFlash('success', 'Un compte a été créé pour le parent');
            }

            return $this->render(
                '@AcMarcheMercrediAdmin/quick/created.html.twig',
                [
                    'enfant' => $enfant,
                    'tuteur' => $tuteur,
                    'user' => $user,
                    'password' => $password,
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/quick/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
