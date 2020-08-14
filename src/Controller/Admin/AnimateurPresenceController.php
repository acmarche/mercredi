<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Animateur\Form\AnimateurJourType;
use AcMarche\Mercredi\Animateur\Form\AnimateurType;
use AcMarche\Mercredi\Animateur\Form\SearchAnimateurType;
use AcMarche\Mercredi\Animateur\Message\AnimateurCreated;
use AcMarche\Mercredi\Animateur\Message\AnimateurDeleted;
use AcMarche\Mercredi\Animateur\Message\AnimateurUpdated;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/animateur/presences")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class AnimateurPresenceController extends AbstractController
{
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var string
     */
    private const ANIMATEUR = 'animateur';
    /**
     * @var AnimateurRepository
     */
    private $animateurRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;


    public function __construct(
        AnimateurRepository $animateurRepository,
        JourRepository $jourRepository
    ) {
        $this->animateurRepository = $animateurRepository;
        $this->jourRepository = $jourRepository;
    }


    /**
     * @Route("/{id}/edit", name="mercredi_admin_animateur_presence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Animateur $animateur): Response
    {
        $form = $this->createForm(AnimateurJourType::class, $animateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            return $this->redirectToRoute('mercredi_admin_animateur_show', ['id' => $animateur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/animateur/presences_edit.html.twig',
            [
                self::ANIMATEUR => $animateur,
                self::FORM => $form->createView(),
            ]
        );
    }

}
