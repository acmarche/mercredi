<?php

namespace AcMarche\Mercredi\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Dto\AssociateUserAnimateurDto;
use AcMarche\Mercredi\User\Form\AssociateAnimateurType;
use AcMarche\Mercredi\User\Handler\AssociationAnimateurHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User controller.
 *
 * @Route("/security/associer/animateur")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class AssocierAnimateurController extends AbstractController
{
    /**
     * @var string
     */
    private const MERCREDI_ADMIN_USER_SHOW = 'mercredi_admin_user_show';
    /**
     * @var string
     */
    private const ID = 'id';
    private AssociationAnimateurHandler $associationAnimateurHandler;
    private AnimateurRepository $animateurRepository;

    public function __construct(
        AssociationAnimateurHandler $associationAnimateurHandler,
        AnimateurRepository $ecoleRepository
    ) {
        $this->associationAnimateurHandler = $associationAnimateurHandler;
        $this->animateurRepository = $ecoleRepository;
    }

    /**
     * @Route("/{id}", name="mercredi_user_associate_animateur", methods={"GET","POST"})
     */
    public function associate(Request $request, User $user): RedirectResponse
    {
        if (!$user->isAnimateur()) {
            $this->addFlash('danger', 'Le compte n\'a pas le rôle de animateur');

            return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
        }

        $associateUserAnimateurDto = new AssociateUserAnimateurDto($user);

        $form = $this->createForm(AssociateAnimateurType::class, $associateUserAnimateurDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationAnimateurHandler->handleAssociateAnimateur($associateUserAnimateurDto);

            return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/associer_animateur.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_user_dissociate_animateur", methods={"DELETE"})
     */
    public function dissociate(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('dissociate'.$user->getId(), $request->request->get('_token'))) {
            $animateurId = (int)$request->request->get('animateur');
            if (0 === $animateurId) {
                $this->addFlash('danger', 'L\'animateur n\'a pas été trouvé');

                return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
            }

            $animateur = $this->animateurRepository->find($animateurId);
            $this->associationAnimateurHandler->handleDissociateAnimateur($user, $animateur);
        }

        return $this->redirectToRoute(self::MERCREDI_ADMIN_USER_SHOW, [self::ID => $user->getId()]);
    }
}
